<?php

namespace Resources\Domain\ValueObject;

use Resources\Exception\RegularException;

class DateTimeInterval extends TimeInterval
{

    const INDONESIAN_DAY_TABLE = [
        '1' => 'senin',
        '2' => 'selasa',
        '3' => 'rabu',
        '4' => 'kamis',
        '5' => "jumat",
        '6' => "sabtu",
        '7' => "minggu",
    ];

    protected $startDateTime = null, $endDateTime = null;

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startDateTime;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endDateTime;
    }

    public function __construct(?\DateTimeImmutable $startDateTime, ?\DateTimeImmutable $endDateTime)
    {
        $this->startDateTime = empty($startDateTime) ?
                $startDateTime : new \DateTimeImmutable($startDateTime->format('Y-m-d H:i:s'));
        $this->endDateTime = empty($endDateTime) ?
                $endDateTime : new \DateTimeImmutable($endDateTime->format('Y-m-d H:i:s'));

        if ($this->getEndTimeStamp() < $this->getStartTimeStamp()) {
            $errorDetail = "bad request: end time must be bigger than or equals start time";
            throw RegularException::badRequest($errorDetail);
        }
    }

    protected function getStartTimeStamp(): float
    {
        return is_null($this->startDateTime) ? -INF : $this->startDateTime->getTimestamp();
    }

    protected function getEndTimeStamp(): float
    {
        return is_null($this->endDateTime) ? INF : $this->endDateTime->getTimestamp();
    }

    public function getTimeDescriptionInIndonesianFormat(): ?string
    {
        if (!isset($this->startDateTime) or !isset($this->endDateTime)) {
            return null;
        }
        $day = self::INDONESIAN_DAY_TABLE[$this->getStartTime()->format('N')];
        $date = $this->getStartTime()->format("d-M-Y");
        $startHour = $this->getStartTime()->format("H:i");
        $endHour = $this->getEndTime()->format("H:i");
        
        return "$day, $date pukul $startHour - $endHour";
    }

    public function getStartDayInIndonesianFormat(): ?string
    {
        return empty($this->getStartTime()) ? null : self::INDONESIAN_DAY_TABLE[$this->getStartTime()->format('N')];
    }

}
