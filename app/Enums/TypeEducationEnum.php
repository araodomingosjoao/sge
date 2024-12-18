<?php
namespace App\Enums;

enum TypeEducationEnum: int
{
    case FUNDAMENTAL_INITIAL = 1;
    case FUNDAMENTAL_FINAL = 2;
    case FUNDAMENTAL_COMPLETE = 3;
    case HIGH_SCHOOL = 4;

    public function label(): string
    {
        return match($this) {
            self::FUNDAMENTAL_INITIAL => 'Ensino Fundamental - Anos Iniciais',
            self::FUNDAMENTAL_FINAL => 'Ensino Fundamental - Anos Finais',
            self::FUNDAMENTAL_COMPLETE => 'Ensino Fundamental Completo',
            self::HIGH_SCHOOL => 'Ensino Médio',
        };
    }
}