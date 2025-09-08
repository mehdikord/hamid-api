<?php
namespace App\Traits\Searching;
//advance searching trait for use in all repository for searching on columns in index functions

trait SearchingTrait
{
    public function advance_search($query)
    {
        //check searching data from request
        if (request()->filled('search')){
            foreach (request()->search as $item){
                if (!empty($item['field']) && isset($item['value']) && !empty($item['condition'])){
                    if ($item['condition'] == 'LIKE'){
                        $query->where($item['field'],"LIKE",'%'.$item['value'].'%');
                    }else{
                        $query->where($item['field'],$item['condition'],$item['value']);
                    }
                }

            }

        }
    }


}
