<?php

namespace App\Models;

use App\Core\Model;

/**
 * Image Model
 * Manages image records in the database
 */
class Image extends Model
{
    protected $table = 'images';
    protected $fillable = [
        'user_id',
        'image_type',
        'original_name',
        'filename',
        'path',
        'file_size',
        'mime_type',
        'width',
        'height',
        'is_admin',
    ];

    /**
     * Get image URL
     */
    public function getUrl()
    {
        return app_base_url($this->attributes['path'] ?? '');
    }

    /**
     * Get admin images
     */
    public static function getAdminImages($type = null)
    {
        $query = self::where('is_admin', true)->whereNull('deleted_at');
        if ($type) {
            $query->where('image_type', $type);
        }
        return $query->get();
    }

    /**
     * Get user images
     */
    public static function getUserImages($userId, $type = null)
    {
        $query = self::where('user_id', $userId)->whereNull('deleted_at');
        if ($type) {
            $query->where('image_type', $type);
        }
        return $query->get();
    }

    /**
     * Get images by type
     */
    public static function getByType($type)
    {
        return self::where('image_type', $type)->whereNull('deleted_at')->get();
    }

    /**
     * Soft delete image
     */
    public function softDelete()
    {
        return $this->update(['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Restore soft deleted image
     */
    public function restore()
    {
        return $this->update(['deleted_at' => null]);
    }
}
