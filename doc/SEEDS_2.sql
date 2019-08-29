insert into rhythm_bars (id, content, length) VALUES
(1,  "[{\"type\" :\"bar\" }]", 0),
(2,  "[{\"type\" :\"n\", \"value\" :4}]", 1),
(3,  "[{\"type\" :\"r\", \"value\" :4}]", 1),
(4,  "[{\"type\" :\"n\", \"value\" :2}]", 2),
(5,  "[{\"type\" :\"n\", \"value\" :2, \"dot\": true}]", 3),
(6,  "[{\"type\" :\"r\", \"value\" :2}]", 2),
(7,  "[{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8}]", 1),
(8,  "[{\"type\" :\"n\", \"value\" :8},{\"type\" :\"r\", \"value\" :8}]", 1),
(9,  "[{\"type\" :\"r\", \"value\" :8},{\"type\" :\"n\", \"value\" :8}]", 1),
(10, "[{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :4},{\"type\" :\"n\", \"value\" :8}]", 2),
(11, "[{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8, \"tie\" :true},{\"type\" :\"n\", \"value\" :8}]", 2),
(12, "[{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8, \"tie\" :true},{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8},{\"type\" :\"n\", \"value\" :8}]", 4),
(13, "[{\"type\" :\"n\", \"value\" :16},{\"type\" :\"n\", \"value\" :16},{\"type\" :\"n\", \"value\" :16},{\"type\" :\"n\", \"value\" :16}]", 1),
(14, "[{\"type\":\"r\",\"value\":8},{\"type\":\"n\",\"value\":16},{\"type\":\"n\",\"value\":16}]", 1);


insert into rhythm_features (id, name, max_occurrences, min_occurrences) VALUES
(1, "Level 11", null, null), 
(2, "Level 12", null, null), 
(3, "Level 13", null, null), 
(4, "Level 14 pavze", null, null);

insert into bar_infos (id, bar_info, min_rhythm_level) VALUES
(1, "{\"num_beats\": 4, \"base_note\": 4}", 11);

insert into rhythm_bar_occurrences (rhythm_bar_id, rhythm_feature_id, bar_probability) VALUES
(2, 1, 0.5),
(3, 1, 0.5),
(4, 1, 0.5),
(5, 1, 0.5),
(6, 1, 0.5),
(7, 2, 0.5),
(8, 2, 0.5),
(9, 2, 0.5),
(10, 2, 0.5),
(11, 2, 0.5),
(12, 2, 0.5),
(13, 3, 0.5),
(14, 3, 0.5),
(3, 4, 0.5),
(8, 4, 0.5),
(9, 4, 0.5),
(6, 4, 0.5);

insert into rhythm_feature_occurrences (rhythm_feature_id, rhythm_level, bar_info_id, feature_probability) VALUES
(1, 11, 1,   1),
(2, 12, 1,   1),
(1, 12, 1, 0.7),
(3, 13, 1,   1),
(2, 13, 1, 0.7),
(1, 13, 1, 0.4),
(4, 14, 1,   1),
(3, 14, 1, 0.2),
(2, 14, 1, 0.2),
(1, 14, 1, 0.2);