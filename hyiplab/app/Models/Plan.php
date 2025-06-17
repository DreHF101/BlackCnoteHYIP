<?php

namespace Blackcnotelab\Models;

use Blackcnotelab\BackOffice\Database\Model;

class Plan extends Model
{
    protected static $table = 'blackcnotelab_plans';

    protected $fillable = [
        'name',
        'description',
        'min_amount',
        'max_amount',
        'interest_rate',
        'term_days',
        'status'
    ];

    public function getActivePlans()
    {
        return $this->where('status', 'active')->get();
    }

    public function getPlanById($id)
    {
        return $this->find($id);
    }
}
