<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

final class NotificationController extends ApiController
{
    #[QueryParameter('page', description: 'Page number', type: 'int', default: 1, example: 1)]
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notifications = $user->notifications()->paginate(15);

        return $this->success($notifications);
    }

    #[QueryParameter('page', description: 'Page number', type: 'int', default: 1, example: 1)]
    public function unread(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notifications = $user->unreadNotifications()->paginate(15);

        return $this->success($notifications);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->find($id);

        if ($notification === null) {
            return $this->notFound('Notification not found');
        }

        $notification->markAsRead();

        return $this->success(message: 'Notification marked as read');
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        return $this->success(message: 'All notifications marked as read');
    }

    #[QueryParameter('page', description: 'Page number', type: 'int', default: 1, example: 1)]
    public function lowStockAlerts(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $alerts = $user->notifications()
            ->where('type', LowStockAlert::class)
            ->paginate(15);

        return $this->success($alerts);
    }
}
