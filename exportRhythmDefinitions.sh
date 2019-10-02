filename="rhythmGeneratingDefinitions.sql"
database="trubadur"


cat > $filename <<HEO 
USE trubadur;

DELETE FROM rhythm_bar_occurrences     WHERE rhythm_bar_id > 0;
DELETE FROM rhythm_feature_occurrences WHERE rhythm_feature_id > 0;
DELETE FROM rhythm_features            WHERE id > 0;

HEO

tables_ign="rhythm_bars bar_infos"

for table in $tables_ign
do

    echo "--- INSERT IGNORE $table" >> $filename

    mysqldump \
	--skip-comments \
	--skip-add-drop-table \
	--skip-add-locks \
	--skip-disable-keys \
	--skip-set-charset \
	--compact \
    --extended-insert=FALSE \
    --insert-ignore \
	--no-create-info \
	-uroot -proot \
	$database $table >> $filename;

    cat >> $filename <<EOL

EOL

done;

tables_normal="rhythm_features rhythm_feature_occurrences rhythm_bar_occurrences"

for table in $tables_normal;
do

    echo "--- INSERT $table" >> $filename

    mysqldump \
        --skip-comments \
        --skip-add-drop-table \
        --skip-add-locks \
        --skip-disable-keys \
        --skip-set-charset \
        --compact \
        --extended-insert=FALSE \
        --no-create-info \
        -uroot -proot \
        $database $table >> $filename;

    cat >> $filename <<EOL

EOL
done;