<?php

namespace App\Model;

class MeetupRequest
{
    public static int $POMODORO_STEP = 30; // real step of pomodoro is 25 minutes

    public static int $MIN_HOUR = 9;
    public static int $MAX_HOUR = 20;

    public string $title;

    public \DateTimeInterface $plannedDayAt;
    public \DateTimeInterface $plannedTimeAt;

    public int $duration;

}
