<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table  = 'lists';

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'list_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childrenLists()
    {
        return $this->hasMany(TodoList::class, 'list_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentList()
    {
        return $this->belongsTo(TodoList::class, 'list_id' , 'id');
    }

    public function scopeFilter(Builder $query){
        return $query->when(request('status'), function ($query) {
            $query->where('status', request('status'))
                ->with('childrenLists', function ($query) {
                   $query->where('status', request('status'));
                });

        })
            ->when(request('priority_from'), function ($query){
               $query->where('priority', '>=', request('priority_from'))
                   ->with('childrenLists', function ($query) {
                       $query->where('priority', '>=', request('priority_from'));
                   });
            })
            ->when(request('priority_to'), function ($query){
                $query->where('priority', '<=', request('priority_to'))
                    ->with('childrenLists', function ($query) {
                        $query->where('priority', '<=', request('priority_to'));
                    });
            })
            ->when(request('sort_by'), function ($query) {
                $query->orderBy(request('sort_by'), request('order_by', 'asc'))
                    ->with('childrenLists', function ($query) {
                        $query->orderBy(request('sort_by'), request('order_by', 'asc'));
                    });
            });
    }
}
