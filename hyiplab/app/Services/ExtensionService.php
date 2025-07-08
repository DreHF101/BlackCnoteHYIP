<?php

namespace Hyiplab\Services;

use Hyiplab\Models\Extension;

class ExtensionService
{
    public function getAllExtensions()
    {
        return Extension::orderBy('name', 'asc')->get();
    }

    public function updateExtension(int $extensionId, array $data): Extension
    {
        $extension = Extension::findOrFail($extensionId);
        
        // Validate required fields based on extension shortcode
        $shortcode = json_decode($extension->shortcode, true);
        $validationRules = [];
        foreach ($shortcode as $key => $val) {
            $validationRules[$key] = 'required';
        }

        // Update shortcode values
        foreach ($shortcode as $key => $value) {
            if (isset($data[$key])) {
                $shortcode[$key]['value'] = $data[$key];
            }
        }

        $extension->shortcode = json_encode($shortcode);
        $extension->save();

        return $extension;
    }

    public function toggleExtensionStatus(int $extensionId): Extension
    {
        $extension = Extension::findOrFail($extensionId);
        $extension->status = !$extension->status;
        $extension->save();

        return $extension;
    }

    public function getExtensionValidationRules(int $extensionId): array
    {
        $extension = Extension::findOrFail($extensionId);
        $shortcode = json_decode($extension->shortcode, true);
        
        $validationRules = [];
        foreach ($shortcode as $key => $val) {
            $validationRules[$key] = 'required';
        }

        return $validationRules;
    }
} 