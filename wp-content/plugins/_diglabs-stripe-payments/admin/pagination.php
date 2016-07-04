<?php

if(!function_exists('diglabs_kriesi_pagination'))
{
    // This function is used to render the pagination elements.
    //
    function diglabs_kriesi_pagination($cur_page=0, $tot_pages=1, $range = 2)
    {
        $tab = 'customers';
        if(isset($_REQUEST['tab']))
        {
            $tab = $_REQUEST['tab'];
        }
        $baseUrl = '?page=' . DLSP_ADMIN_PAGE . "&tab=" . $tab;
        if(isset($_REQUEST['filter']))
        {
            $baseUrl .= "filter=" . $_REQUEST['filter'];
        }

        $showitems = ($range * 2)+1;  

        if(1 != $tot_pages)
        {
            echo "<div class='pagination'>";
            if($cur_page > 2 && $cur_page > $range+1 && $showitems < $tot_pages) 
            {
                echo "<a href='" . $baseUrl . "&p=0'>&laquo;</a>";
            }
            if($cur_page > 1 && $showitems < $tot_pages)
            {
                echo "<a href='" . $baseUrl . "&p=" . ($cur_page-1) . "'>&lsaquo;</a>";
            }

            for ($i=0; $i < $tot_pages; $i++)
            {
                if (0 != $tot_pages &&( !($i >= $cur_page+$range+1 || $i <= $cur_page-$range-1) || $tot_pages <= $showitems ))
                {
                    echo ($cur_page == $i) ? "<span class='current'>".($i+1)."</span>" : "<a href='" . $baseUrl . "&p=" . $i . "' class='inactive' >".($i+1)."</a>";
                }
            }

            if ($cur_page < $tot_pages-1 && $showitems < $tot_pages)
            {
                echo "<a href='" . $baseUrl . "&p=" . ($cur_page+1) . "'>&rsaquo;</a>";
            }
            if ($cur_page < $tot_pages-2 &&  $cur_page+$range-1 < $tot_pages && $showitems < $tot_pages) 
            {
                echo "<a href='" . $baseUrl . "&p=".($tot_pages-1)."'>&raquo;</a>";
            }
            echo "</div>\n";
        }
    }
}
