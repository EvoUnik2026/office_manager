<?php

declare(strict_types=1);

namespace App\Domain\Office;

enum RoomType: string
{
    case Office = 'OFFICE';
    case MeetingRoom = 'MEETING_ROOM';
    case ServerRoom = 'SERVER_ROOM';
    case Hall = 'HALL';
    case Storage = 'STORAGE';
    case Other = 'OTHER';
}
