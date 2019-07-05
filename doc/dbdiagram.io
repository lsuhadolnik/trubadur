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