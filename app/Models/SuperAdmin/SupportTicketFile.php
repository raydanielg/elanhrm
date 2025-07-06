<?php

namespace App\Models\SuperAdmin;

use App\Traits\IconTrait;
use App\Models\BaseModel;

/**
 * App\Models\SuperAdmin\SupportTicketFile
 *
 * @property-read mixed $file_url
 * @method static Builder|SupportTicketFile newModelQuery()
 * @method static Builder|SupportTicketFile newQuery()
 * @method static Builder|SupportTicketFile query()
 * @mixin Eloquent
 * @property int $id
 * @property int $user_id
 * @property int $support_ticket_reply_id
 * @property string $filename
 * @property string|null $description
 * @property string|null $google_url
 * @property string|null $hashname
 * @property string|null $size
 * @property string|null $dropbox_link
 * @property string|null $external_link
 * @property string|null $external_link_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $icon
 * @method static Builder|SupportTicketFile whereCreatedAt($value)
 * @method static Builder|SupportTicketFile whereDescription($value)
 * @method static Builder|SupportTicketFile whereDropboxLink($value)
 * @method static Builder|SupportTicketFile whereExternalLink($value)
 * @method static Builder|SupportTicketFile whereExternalLinkName($value)
 * @method static Builder|SupportTicketFile whereFilename($value)
 * @method static Builder|SupportTicketFile whereGoogleUrl($value)
 * @method static Builder|SupportTicketFile whereHashname($value)
 * @method static Builder|SupportTicketFile whereId($value)
 * @method static Builder|SupportTicketFile whereSize($value)
 * @method static Builder|SupportTicketFile whereSupportTicketReplyId($value)
 * @method static Builder|SupportTicketFile whereUpdatedAt($value)
 * @method static Builder|SupportTicketFile whereUserId($value)
 */
class SupportTicketFile extends BaseModel
{

    use IconTrait;

    const FILE_PATH = 'support-ticket-files';

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('support-ticket-files/' . $this->support_ticket_reply_id . '/' . $this->hashname);
    }

}
