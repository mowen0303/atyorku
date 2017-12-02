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

export_jason_db()
{
    printf "Export to single file? (y/n): "
    read op3
    if [ $op3 = "y" ]; then
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku image >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku professor >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --password='' atyorku course_code >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --password='' atyorku book_category > ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku book >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku course_rating >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku course_prof_report >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku course_report >> ./jason/schema.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku professor_report >> ./jason/schema.sql
    else
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku book > ./jason/book.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --password='' atyorku book_category > ./jason/book_category.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --password='' atyorku course_code > ./jason/course_code.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku course_prof_report > ./jason/course_prof_report.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku course_rating > ./jason/course_rating.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku course_report > ./jason/course_report.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku image > ./jason/image.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku professor_report > ./jason/professor_report.sql
        /Applications/xampp/xamppfiles/bin/mysqldump --user=root --no-data --password='' atyorku professor > ./jason/professor.sql
    fi
}

echo "1) Import database"
echo "2) Export whole database"
echo "3) Export Jason database tables"
printf "Make your selection: "
read op1
if [ $op1 = "1" ]; then
    import_db
elif [ $op1 = "2" ]; then
    export_db
else
    export_jason_db
fi
echo "===== Program Completed ====="
