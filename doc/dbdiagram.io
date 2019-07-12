Table rhythm_exercise_bars{
  rhythm_exercise_id int [ref: > rhythm_exercises.id]
  rhythm_bar_id int [ref: > rhythm_bars.id]
  seq int [not null]
}

Table rhythm_exercises {
  id int [pk]
  name varchar [not null]
  barInfo json [not null]
  BPM int [not null]
  difficulty int
  description text
}

Table rhythm_bars {
  id int [pk]
  content json [not null]
  barInfo json [not null]
  difficulty int
}

Table games {
  id int [pk]
  difficulty_id int(10)
  mode enum('practice','single','multi')
  type enum('intervals','rhythm')
  created_at timestamp
  updated_at timestamp
}

Table users{
  id int [pk]
  email varchar(191)
  name varchar(191)
  admin tinyint(1)
  rating int(11)
}

Table questions {
  id int [pk]
}

Table answers {
  id int [pk]
  game_id int [ref: > games.id]
  user_id int [ref: > users.id]
  question_id int [ref: > questions.id]
  time int
  n_additions int [note: "kolikokrat je uporabnik pritisnil na tipkovnico"]
  n_deletions int [note: "Kolikokrat je uporabnik pritisnil na izbriši"]
  n_playbacks int [note: "kolikokrat je uporabnik predvajal vajo"]
  n_answer int [note: "kolikokrat je uporabnik poskusil oddati vajo"]
  success int [note: "Ali je bil uporabnik uspešen pri izpolnjevanju"]
  created_at timestamp
  updated_at timestamp
}

Table difficulties {
  id         int(10)
  range      int(11)        
  min_notes  int(11)        
  max_notes  int(11)        
  created_at timestamp      
  updated_at timestamp
}
Table game_user {
  game_id    int(10)
  user_id    int(10)
  instrument enum('clarinet','guitar','piano','trumpet','violin')
  points     int(11)      
  finished   tinyint(1)
  created_at timestamp
  updated_at timestamp
}

Table levels {
  id         int(10)
  level      int(11)
  label      varchar(191)
  image      varchar(191)
  min_rating int(11)
  max_rating int(11)
  created_at timestamp
  updated_at timestamp
}