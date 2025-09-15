<?php
namespace App\Traits;
//advance searching trait for use in all repository for searching on columns in index functions

trait SearchingTrait
{
    public function advance_search($query)
    {
        //check searching data from request
        if (request()->filled('search')){
            foreach (request()->search as $item){
                if (!empty($item['field']) && isset($item['value']) && !empty($item['condition'])){
                    // Check if field contains a dot (indicating a relation field)
                    if(strpos($item['field'], '.') !== false){
                        //get relation model
                        $field = $item['field'];
                        $relation_parts = explode('.',$field);
                        $relation = $relation_parts[0];
                        $relation_field = $relation_parts[1];

                        $query->whereHas($relation,function($subQuery) use ($relation_field,$item){
                            if($item['condition'] == 'LIKE'){
                                $subQuery->where($relation_field,"LIKE",'%'.$item['value'].'%');
                            }else{
                                $subQuery->where($relation_field,$item['condition'],$item['value']);
                            }
                        });
                    }elseif($item['type'] == 'date'){
                        $query->whereDate($item['field'],$item['condition'],$item['value']);
                    }else{
                        // Direct field search
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


}
