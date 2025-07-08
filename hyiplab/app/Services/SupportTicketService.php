<?php

namespace Hyiplab\Services;

use Hyiplab\Lib\FormProcessor;
use Hyiplab\Models\SupportAttachment;
use Hyiplab\Models\SupportMessage;
use Hyiplab\Models\SupportTicket;
use Hyiplab\Models\Form;
use Hyiplab\Cache\CacheManager;
use Hyiplab\Database\QueryOptimizer;
use Hyiplab\Log\Logger;

class SupportTicketService
{
    private CacheManager $cache;
    private QueryOptimizer $queryOptimizer;
    private const CACHE_TTL = 1800; // 30 minutes

    public function __construct()
    {
        $this->cache = CacheManager::getInstance();
        $this->queryOptimizer = new QueryOptimizer();
    }

    public function getTickets(array $filters = [], int $paginate = 20)
    {
        $cacheKey = 'support_tickets_' . md5(serialize($filters));
        
        return $this->cache->remember($cacheKey, function () use ($filters, $paginate) {
            $query = SupportTicket::query();
            
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            if (!empty($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }
            
            if (!empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
            
            // Optimize with select fields
            $query = $this->queryOptimizer->selectFields($query, [
                'id', 'ticket_number', 'subject', 'status', 'priority', 
                'user_id', 'created_at', 'updated_at'
            ]);
            
            return $this->queryOptimizer->optimizePaginated($query, $paginate, $cacheKey);
        }, self::CACHE_TTL);
    }

    public function replyToTicket(int $ticketId, string $message, array $attachments = []): SupportMessage
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        
        $supportMessage = new SupportMessage();
        $supportMessage->support_ticket_id = $ticketId;
        $supportMessage->admin_id = get_current_user_id();
        $supportMessage->message = sanitize_textarea_field($message);
        $supportMessage->attachments = !empty($attachments) ? maybe_serialize($attachments) : null;
        $supportMessage->save();

        // Update ticket status
        $ticket->status = 1; // Answered
        $ticket->save();

        // Clear cache
        $this->clearTicketCache($ticketId);

        Logger::info('Admin replied to support ticket', [
            'ticket_id' => $ticketId,
            'admin_id' => get_current_user_id(),
            'message_length' => strlen($message)
        ]);

        return $supportMessage;
    }

    public function closeTicket(int $ticketId): SupportTicket
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->status = 2; // Closed
        $ticket->save();

        // Clear cache
        $this->clearTicketCache($ticketId);

        Logger::info('Support ticket closed', ['ticket_id' => $ticketId]);

        return $ticket;
    }

    public function deleteMessage(int $messageId): bool
    {
        $message = SupportMessage::findOrFail($messageId);
        $ticketId = $message->support_ticket_id;
        
        $deleted = $message->delete();
        
        if ($deleted) {
            // Clear cache
            $this->clearTicketCache($ticketId);
            
            Logger::warning('Support message deleted', [
                'message_id' => $messageId,
                'ticket_id' => $ticketId,
                'admin_id' => get_current_user_id()
            ]);
        }

        return $deleted;
    }

    protected function storeAttachments(int $messageId, array $files): void
    {
        $path = hyiplab_file_path('ticket');
        
        foreach ($files as $file) {
            try {
                $attachment = new SupportAttachment();
                $attachment->support_message_id = $messageId;
                $attachment->attachment = hyiplab_file_uploader($file, $path);
                $attachment->save();
            } catch (\Exception $e) {
                throw new \RuntimeException('File could not be uploaded: ' . $e->getMessage());
            }
        }
    }

    public function getTicketWithMessages(int $ticketId): array
    {
        $cacheKey = "ticket_messages_{$ticketId}";
        
        return $this->cache->remember($cacheKey, function () use ($ticketId) {
            $ticket = SupportTicket::with(['messages' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->findOrFail($ticketId);

            return [
                'ticket' => $ticket,
                'messages' => $ticket->messages
            ];
        }, self::CACHE_TTL);
    }

    public function getUserTickets(int $userId, int $paginate = 20)
    {
        $cacheKey = "user_tickets_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($userId, $paginate) {
            $query = SupportTicket::where('user_id', $userId)
                ->orderBy('id', 'desc');
            
            return $this->queryOptimizer->optimizePaginated($query, $paginate, $cacheKey);
        }, self::CACHE_TTL);
    }

    public function createUserTicket(int $userId, array $data): SupportTicket
    {
        // Rate limiting for ticket creation
        $rateLimitKey = "ticket_creation_{$userId}";
        if (rate_limit($rateLimitKey, 5, 300)) { // Max 5 tickets per 5 minutes
            throw new \InvalidArgumentException('Too many ticket creation attempts. Please wait before creating another ticket.');
        }

        $form = Form::where('act', 'support_ticket')->first();
        if (!$form) {
            throw new \RuntimeException('Support ticket form not found');
        }

        $formDataObj = json_decode(json_encode(maybe_unserialize($form->form_data)));
        $formProcessor = new FormProcessor();
        $userData = $formProcessor->processFormData($data, $formDataObj);

        $ticket = new SupportTicket();
        $ticket->ticket_number = 'TIC' . time() . rand(1000, 9999);
        $ticket->user_id = $userId;
        $ticket->subject = sanitize_text_field($data['subject']);
        $ticket->message = sanitize_textarea_field($data['message']);
        $ticket->status = 0; // Open
        $ticket->priority = $data['priority'] ?? 'medium';
        $ticket->attachments = !empty($data['attachments']) ? maybe_serialize($data['attachments']) : null;
        $ticket->save();

        // Clear user tickets cache
        $this->cache->forget("user_tickets_{$userId}");

        Logger::info('Support ticket created', [
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'subject' => $ticket->subject
        ]);

        return $ticket;
    }

    public function getUserTicketByTicketNumber(string $ticketNumber, int $userId): SupportTicket
    {
        $cacheKey = "user_ticket_{$ticketNumber}_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($ticketNumber, $userId) {
            return SupportTicket::where('ticket_number', $ticketNumber)
                ->where('user_id', $userId)
                ->firstOrFail();
        }, self::CACHE_TTL);
    }

    public function closeUserTicket(int $ticketId, int $userId): SupportTicket
    {
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        $ticket->status = 2; // Closed
        $ticket->save();

        // Clear cache
        $this->clearTicketCache($ticketId);
        $this->cache->forget("user_tickets_{$userId}");

        Logger::info('User closed support ticket', [
            'ticket_id' => $ticketId,
            'user_id' => $userId
        ]);

        return $ticket;
    }

    public function replyToUserTicket(int $ticketId, int $userId, string $message, array $attachments = []): SupportMessage
    {
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $supportMessage = new SupportMessage();
        $supportMessage->support_ticket_id = $ticketId;
        $supportMessage->user_id = $userId;
        $supportMessage->message = sanitize_textarea_field($message);
        $supportMessage->attachments = !empty($attachments) ? maybe_serialize($attachments) : null;
        $supportMessage->save();

        // Update ticket status
        $ticket->status = 1; // Answered
        $ticket->save();

        // Clear cache
        $this->clearTicketCache($ticketId);
        $this->cache->forget("user_tickets_{$userId}");

        Logger::info('User replied to support ticket', [
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'message_length' => strlen($message)
        ]);

        return $supportMessage;
    }

    public function getTicketWithMessagesByTicketNumber(string $ticketNumber, int $userId): array
    {
        $cacheKey = "user_ticket_messages_{$ticketNumber}_{$userId}";
        
        return $this->cache->remember($cacheKey, function () use ($ticketNumber, $userId) {
            $ticket = SupportTicket::with(['messages' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->where('ticket_number', $ticketNumber)
                ->where('user_id', $userId)
                ->firstOrFail();

            return [
                'ticket' => $ticket,
                'messages' => $ticket->messages
            ];
        }, self::CACHE_TTL);
    }

    /**
     * Clear cache for a specific ticket
     */
    private function clearTicketCache(int $ticketId): void
    {
        $this->cache->forget("ticket_messages_{$ticketId}");
    }

    /**
     * Clear all support ticket cache
     */
    public function clearAllCache(): void
    {
        $this->cache->forget('support_tickets_*');
        Logger::info('Support ticket cache cleared');
    }
} 