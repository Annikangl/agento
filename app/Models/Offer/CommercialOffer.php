<?php

namespace App\Models\Offer;

use App\Enums\CommercialOfferStatus;
use App\Exceptions\Api\Services\PDFCreateException;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Response;

class CommercialOffer extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ERROR = 'error';

    const TYPE_DUBIZZLE = 'dubizzle';
    const TYPE_PROPERTY = 'property';
    const TYPE_BAYUT = 'bayut';

    protected $fillable = [
        'user_id',
        'lang',
        'source_name',
        'source_link',
        'title',
        'status',
        'pdf_path',
    ];

    protected $casts = [
        'status' => CommercialOfferStatus::class,
    ];

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    public function setStatusError(string $message): void
    {
        $this->title = $message;
        $this->status = self::STATUS_ERROR;
        $this->save();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Get source name from url
     * @param string $sourceLink
     * @return string
     * @throws PDFCreateException
     */
    public static function getSourceName(string $sourceLink): string
    {
        switch ($sourceLink) {
            case str_contains($sourceLink, 'propertyfinder.ae'):
                return CommercialOffer::TYPE_PROPERTY;
            case str_contains($sourceLink, 'dubizzle.com'):
                return CommercialOffer::TYPE_DUBIZZLE;
            case str_contains($sourceLink, 'bayut.com'):
                return CommercialOffer::TYPE_BAYUT;
            default:
                throw new PDFCreateException('Undefined source link',
                    Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_COMPLETED => 'Успешные',
            self::STATUS_ERROR => 'Неуспешные',
            self::STATUS_PENDING => 'В процессе формирования',
        ];
    }
}
