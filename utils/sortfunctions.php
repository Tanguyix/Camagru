<?php
    function sortbylikes($a, $b) {
        return($b['nb_likes'] - $a['nb_likes']);
    }

    function sortbycoms($a, $b) {
        return($b['nb_coms'] - $a['nb_coms']);
    }

    function sortalpha($a, $b) {
        return(strcasecmp($a['photo_infos']['name'], $b['photo_infos']['name']));
    }

    function sortbypics($a, $b) {
        return($b['photo_infos']['nb_photos'] - $a['photo_infos']['nb_photos']);
    }
