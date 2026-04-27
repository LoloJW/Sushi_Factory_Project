<?php       
namespace App\Enum;

enum ReservationType: string {
    case MEETING = 'meeting';
    case AFTER_WORK = 'after_work';
    case ATELIER = 'atelier';
    case MEETING_GLOBAL = 'meetingGlobal';
    case EXTERNE = 'externe';
}