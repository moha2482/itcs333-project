<?php

class Roomcard {
    private int $roomId;
    private string $roomTitle;
    private string $roomImage;
    private float $roomPrice;
    private int $roomSeats;
    private int $roomProjectors;
    private int $roomwhiteboards;

    public function __construct($roomId, $roomTitle, $roomImage, $roomSeats, $roomProjectors, $roomwhiteboards) {
        $this->roomId = $roomId;
        $this->roomTitle = $roomTitle;
        $this->roomImage = $roomImage;
        $this->roomSeats = $roomSeats;
        $this->roomProjectors = $roomProjectors;
        $this->roomwhiteboards = $roomwhiteboards;
    }

    public function display() {
        return ("
        <div class=\"roomcard\">
            <div class=\"roomcard__image\">
                <img src=\"{$this->roomImage}\" alt=\"Room Image\" class=\"roomcard__img\">
            </div>
            <div class=\"roomcard__content\">
                <h3 class=\"roomcard__title\">{$this->roomTitle}</h3>
                <div class=\"roomcard__details\">
                    <span class=\"roomcard__detail\">
                        <i class=\"fas fa-users roomcard__icon\"></i>
                        <span class=\"roomcard__text\">{$this->roomSeats} Seats</span>
                    </span>
                    <span class=\"roomcard__detail\">
                        <i class=\"fas fa-chalkboard-teacher roomcard__icon\"></i>
                        <span class=\"roomcard__text\">{$this->roomProjectors} Projector</span>
                    </span>
                    <span class=\"roomcard__detail\">
                        <i class=\"fas fa-chalkboard roomcard__icon\"></i>
                        <span class=\"roomcard__text\">{$this->roomwhiteboards} Whiteboards</span>
                    </span>
                </div>
                <a href=\"room.php?id={$this->roomId}\" class=\"roomcard__btn roomcard__btn--primary\">
                    Book Now
                </a>
            </div>
        </div>
        ");
    }
}

?>