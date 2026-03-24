<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'author',
        'title',
        'description',
        'technologies',
        'images',
        'project_url',
        'repo_url',
        'year',
        'winner',
        'likes',
        'comments_count',
    ];

    protected $casts = [
        'images' => 'array',
        'winner' => 'integer',
        'year' => 'integer',
        'likes' => 'integer',
        'comments_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->with('user')->orderBy('created_at', 'asc');
    }
}
