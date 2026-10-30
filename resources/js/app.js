//
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.reorder_games = function(id, group_id, position){
    fetch('/games/reorder',{
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ 
            id: id, 
            group_id: group_id,
            position: position 
        })
    });
}