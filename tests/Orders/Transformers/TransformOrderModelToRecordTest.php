<?php

declare(strict_types=1);

namespace Tests\Orders\Transformers;

use App\Orders\Models\OrderModel;
use App\Orders\Transformers\TransformOrderModelToRecord;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use function assert;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderModelToRecordTest extends TestCase
{
    public function testTransformer() : void
    {
        $model = new OrderModel();

        $model->id = 'fooId';

        $model->oldOrderNumber = 1701;

        $user        = new UserModel();
        $user->id    = '1701-A';
        $model->user = $user;

        $model->stripeId = 'USS';

        $model->stripeAmount = 1701.2;

        $model->stripeBalanceTransaction = 'Enterprise';

        $model->stripeCaptured = true;

        $model->stripeCreated = 1;

        $model->stripeCurrency = 'Latinum';

        $model->stripePaid = true;

        $model->subtotal = 1701.3;

        $model->tax = 2836;

        $model->total = 1071.23;

        $model->name = 'Jean-Luc Picard';

        $model->company = 'Starfleet';

        $model->phoneNumber = '1701-D';

        $model->country = 'France';

        $model->address = 'Family Chateau';

        $model->addressContinued = 'Vineyard';

        $model->city = 'La Barre';

        $model->state = 'Bourgogne-Franche-Comté';

        $model->postalCode = '39700';

        $date = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            '2010-02-11T01:26:03+00:00',
        );

        assert($date instanceof DateTimeImmutable);

        $model->date = $date;

        $transformer = new TransformOrderModelToRecord();

        $record = $transformer($model);

        self::assertSame(
            $model->id,
            $record->id,
        );

        self::assertSame(
            $model->oldOrderNumber,
            $record->old_order_number,
        );

        self::assertSame(
            $user->id,
            $record->user_id,
        );

        self::assertSame(
            $model->stripeId,
            $record->stripe_id,
        );

        self::assertSame(
            $model->stripeAmount,
            $record->stripe_amount,
        );

        self::assertSame(
            $model->stripeBalanceTransaction,
            $record->stripe_balance_transaction,
        );

        self::assertSame(
            '1',
            $record->stripe_captured,
        );

        self::assertSame(
            $model->stripeCreated,
            $record->stripe_created,
        );

        self::assertSame(
            $model->stripeCurrency,
            $record->stripe_currency,
        );

        self::assertSame(
            '1',
            $record->stripe_paid,
        );

        self::assertSame(
            $model->subtotal,
            $record->subtotal,
        );

        self::assertSame(
            $model->tax,
            $record->tax,
        );

        self::assertSame(
            $model->total,
            $record->total,
        );

        self::assertSame(
            $model->name,
            $record->name,
        );

        self::assertSame(
            $model->company,
            $record->company,
        );

        self::assertSame(
            $model->phoneNumber,
            $record->phone_number,
        );

        self::assertSame(
            $model->country,
            $record->country,
        );

        self::assertSame(
            $model->address,
            $record->address,
        );

        self::assertSame(
            $model->addressContinued,
            $record->address_continued,
        );

        self::assertSame(
            $model->city,
            $record->city,
        );

        self::assertSame(
            $model->state,
            $record->state,
        );

        self::assertSame(
            $model->postalCode,
            $record->postal_code,
        );

        self::assertSame(
            $model->date->format(DateTimeInterface::ATOM),
            $record->date,
        );
    }
}
