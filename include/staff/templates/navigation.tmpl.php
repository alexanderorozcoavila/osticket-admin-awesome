<?php
if(($tabs=$nav->getTabs()) && is_array($tabs)){
    foreach($tabs as $name =>$tab) {
        if ($tab['href'][0] != '/')
            $tab['href'] = ROOT_PATH . 'scp/' . $tab['href'];
        echo sprintf('<li class="%s %s" style="color:#212331;"><a href="%s">%s</a>',
            $tab['active'] ? 'active':'inactive',
            @$tab['class'] ?: '',
            $tab['href'],$tab['desc']);
        if(!$tab['active'] && ($subnav=$nav->getSubMenu($name))){
            echo "<ul style=\"color:#212331;\">\n";
            foreach($subnav as $k => $item) {
                if (!($id=$item['id']))
                    $id="nav$k";
                if ($item['href'][0] != '/')
                    $item['href'] = ROOT_PATH . 'scp/' . $item['href'];

                echo sprintf(
                    '<li style="color:#212331;"><a class="%s" href="%s" title="%s" id="%s">%s</a></li>',
                    $item['iconclass'],
                    $item['href'], $item['title'],
                    $id, $item['desc']);
            }
            echo "\n</ul>\n";
        }
        echo "\n</li>\n";
    }
} ?>
