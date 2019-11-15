--- Igre po dnevih in igralcih - to bi bil lahko dober graf
select DATE(g.created_at), u.name, count(*) 
	from games g 
    join game_user gu on g.id = gu.game_id 
    join users u on gu.user_id = u.id
    where u.id != 1 and gu.finished = 1
group by DATE(g.created_at), u.name;


