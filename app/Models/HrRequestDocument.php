<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrRequestDocument extends Model
{
    use HasFactory;

    protected $table = 'hr_request_documents';

    protected $fillable = [
        'request_id',
        'document_name',
        'document_type',
        'file_path',
        'uploaded_by',
    ];

    public function request()
    {
        return $this->belongsTo(HrRequest::class, 'request_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
