<?php

namespace App\Models\SuperAdmin;

use App\Models\User;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\SuperAdmin\SupportTicketObserver;

/**
 * App\Models\SuperAdmin\SupportTicket
 *
 * @property-read User $agent
 * @property-read User $client
 * @property-read mixed $created_on
 * @property-read mixed $updated_on
 * @property-read Collection|SupportTicketReply[] $reply
 * @property-read int|null $reply_count
 * @property-read User $requester
 * @method static Builder|SupportTicket newModelQuery()
 * @method static Builder|SupportTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|SupportTicket onlyTrashed()
 * @method static Builder|SupportTicket query()
 * @method static \Illuminate\Database\Query\Builder|SupportTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SupportTicket withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property int $user_id
 * @property int $created_by
 * @property string $subject
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property int|null $agent_id
 * @property int|null $support_ticket_type_id
 * @property int|null $company_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SupportTicket whereAgentId($value)
 * @method static Builder|SupportTicket whereCreatedAt($value)
 * @method static Builder|SupportTicket whereCreatedBy($value)
 * @method static Builder|SupportTicket whereDeletedAt($value)
 * @method static Builder|SupportTicket whereDescription($value)
 * @method static Builder|SupportTicket whereId($value)
 * @method static Builder|SupportTicket wherePriority($value)
 * @method static Builder|SupportTicket whereStatus($value)
 * @method static Builder|SupportTicket whereSubject($value)
 * @method static Builder|SupportTicket whereSupportTicketTypeId($value)
 * @method static Builder|SupportTicket whereUpdatedAt($value)
 * @method static Builder|SupportTicket whereUserId($value)
 */
class SupportTicket extends BaseModel
{

    use HasCompany;

    use SoftDeletes;

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['created_on', 'updated_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(SupportTicketObserver::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active', CompanyScope::class]);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id')->withoutGlobalScopes(['active', CompanyScope::class]);
    }

    public function reply(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'support_ticket_id');
    }

    public function latestReply(): HasOne
    {
        return $this->hasOne(SupportTicketReply::class, 'support_ticket_id')->latest();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function getCreatedOnAttribute()
    {
        if (is_null($this->created_at)) {
            return '';
        }

        return $this->created_at->format('d M Y H:i');

    }

    public function getUpdatedOnAttribute()
    {
        if (is_null($this->updated_at)) {
            return '';
        }

        return $this->updated_at->format('Y-m-d H:i a');

    }

    public function badge($tag = 'p')
    {

        $latestReplyUser = $this->latestReply?->user;
        $totalReply = $this->reply()->count();

        $selfReplyCount = $this->reply()->where('user_id', $latestReplyUser?->id)->count();

        if ($totalReply > 1 && ($totalReply !== $selfReplyCount) && $latestReplyUser && $latestReplyUser->id !== user()->id) {
            return '<' . $tag . ' class="mb-0"><span class="badge badge-secondary mr-1 bg-info">' . __('app.newResponse') . '</span></' . $tag . '>';
        }

        return $totalReply == 1 || ($totalReply == $selfReplyCount) ? '<' . $tag . ' class="mb-0"><span class="badge badge-secondary mr-1 bg-dark-green">' . __('app.new') . '</span></' . $tag . '>' : '';
    }

}
