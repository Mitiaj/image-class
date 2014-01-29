<?php

interface IDriver {

    /**
     * @param stdClass $dimensions
     * @return string path to new thumbnail
     */
    public function createThumb(stdClass $dimensions);

    /**
     * @param $color hex color
     * @return string path to cropped image
     */
    public function cropColor($color);


}