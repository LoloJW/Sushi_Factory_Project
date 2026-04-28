<?php       
namespace App\Enum;

enum ReservationType: string {
    case Meeting = 'meeting';
    case Afterwork = 'after_work';
    case Atelier = 'atelier';
    case MeetingGlobal = 'meetingGlobal';
    case Externe = 'externe';
}