#!/bin/sh

import_db()
{
    echo "Enter q to quit"
    while :
    do
        printf "Please enter import SQL filename: "
        read input
        if [ $input = "q" ]; then
            break;
        elif [ -e $input ]; then
            /Applications/xampp/xamppfiles/bin/mysql --user=root --password='' atyorku < $input
        else
            echo "File '$input' not found."
        fi
    done
    # echo "Importing SQL Files from sql files in `pwd` to atYorku database..."
    # if [ -e "__tmp_schema.sql" ]; then
    #     rm "__tmp_schema.sql"
    # fi
    # touch "__tmp_schema.sql"
    # TABLES=(
    #     "course_code"
    #     "professor"
    #     "image"
    #     "book_category"
    #     "book"
    #     "course_rating"
    #     "course_prof_report"
    #     "course_report"
    #     "professor_report" )
    # for t in ${TABLES[@]}
    # do
    #     cat "./jason/$t.sql" >> "__tmp_schema.sql"
    # done
    # if [ -e "__tmp_schema.sql" ]; then
    #     /Applications/xampp/xamppfiles/bin/mysql -u root atyorku -p < "__tmp_schema.sql" & echo & rm "__tmp_schema.sql"
    #     echo "Import Completed."
    # else
    #     echo "Fail to create __tmp_schema.sql"
    # fi
}

export_db()
{
    printf "Include data? (y/n): "
    read op2
    echo "Exporting atyorku db to schema.sql"
    if [ $op2 = "y" ]; then
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --password='' atyorku > schema.sql
    else
        /Applications/xampp/xamppfiles/bin/mysqldump -user=root --no-data --password='' atyorku > schema.sql
    fi
    echo "Export Completed."
}

echo "1) Import database"
echo "2) Export database"
printf "Make your selection: "
read op1
if [ $op1 = "1" ]; then
    import_db
else
    export_db
fi
echo "===== Program Completed ====="
