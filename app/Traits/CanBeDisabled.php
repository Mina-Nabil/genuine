<?php

namespace App\Traits;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait CanBeDisabled
{
    /**
     * @return Builder query of active memebers only
     */
    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return Builder query of active memebers only
     */
    public function scopeInactive($query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * 
     * @return bool if action succeeded
     */
    public function toggle(): bool
    {
        return $this->setState(!$this->is_active);
    }

    /**
     * 
     * @return bool if action succeeded
     */
    public function deactivate(): bool
    {
        return $this->setState(false);
    }

    /**
     * 
     * @return bool if action succeeded
     */
    public function activate(): bool
    {
        return $this->setState(true);
    }

    /**
     * 
     * @return bool if action succeeded
     */
    public function setState(bool $state): bool
    {
        $this->is_active = $state;
        try {
            if ($this->save()) {
                AppLog::info("Object " . ($state ? 'Activated' : 'De-activated'), self::class . " $this->id status changed");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            report($e);
            AppLog::error("Changing status failed", $e->getMessage());
            return false;
        }
    }
}
