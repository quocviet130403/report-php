<?php

$emojis = [
    'Anger' => '/emojis/Anger.jpg',
    'Fear' => '/emojis/Fear.png',
    'Anxiety' => '/emojis/Anxiety.png',
    'Loss' => '/emojis/Loss.png',
    'Sadness' => '/emojis/Sadness.png',
    'Resignation' => '/emojis/Resignation.png',
    'Guilt' => '/emojis/Guilt.png',
    'Shame' => '/emojis/Shame.png',
    'Jealousy' => '/emojis/Jealousy.png',
    'Enthusiasm' => '/emojis/Enthusiasm.png',
    'Tenderness' => '/emojis/Tenderness.png',
    'Hope' => '/emojis/Hope.png',


];


$emoji_text = [

    'Anger' => $trans->phrase("user_ticket_phrase31"),

    'Fear' => $trans->phrase("user_ticket_phrase32"),
    'Anxiety' => $trans->phrase("user_ticket_phrase33"),
    'Loss' => $trans->phrase("user_ticket_phrase34"),
    'Sadness' => $trans->phrase("user_ticket_phrase35"),

    'Resignation' => $trans->phrase("user_ticket_phrase36"),

    'Guilt' => $trans->phrase("user_ticket_phrase37"),
    'Shame' => $trans->phrase("user_ticket_phrase38"),
    'Jealousy' => $trans->phrase("user_ticket_phrase39"),

    'Enthusiasm' => $trans->phrase("user_ticket_phrase40"),
    'Tenderness' => $trans->phrase("user_ticket_phrase41"),
    'Hope' => $trans->phrase("user_ticket_phrase42"),

];
define("EMOJI_TEXT", $emoji_text);
define("EMOJIS", $emojis);
$ratings = [
    0 => null,
    $trans->phrase("user_ticket_phrase34") => '/emojis/Loss.png',
    $trans->phrase("user_ticket_phrase31") => '/emojis/Resignation.png',
    $trans->phrase("user_ticket_phrase42") => '/emojis/Hope.png',
    $trans->phrase("user_ticket_phrase41") => '/emojis/Tenderness.png',
    $trans->phrase("user_ticket_phrase40") => '/emojis/Enthusiasm.png',




];

define("RATINGS", $ratings);