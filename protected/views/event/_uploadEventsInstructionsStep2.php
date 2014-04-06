    <dl>
        <dt>
            What Settings to change
        </dt>
        <dd>
            If you are uploading <strong>Ice For Sale</strong> in a standard
            CSV file with field headers as the first row and the file contains
            only new records, then you can simply click the continue button
            below. Otherwise, please select the options for your file and click
            the continue button to proceed.
        </dd>
        <dt>
            Event Type
        </dt>
        <dd>
            Specify which type of events you are uploading. The default is set
            to Ice For Sale.
        </dd>
        <dt>
            Field Header Row
        </dt>
        <dd>
            Specify which row (1 - 10) that the field header is located. Data
            will be imported starting after that row and the field headers will
            be used on the next screen to assist in mapping the file data to
            database fields. Although not absolutely required, if your data
            file does not have a header row, then the data row (1 -10) will be
            used as the header and will not be imported. Automatic field
            mapping will also likely not work in this case.
        </dd>
        <dt>
            Field Separator
        </dt>
        <dd>
            Select the field separator character that the data file uses. For a
            CSV file, the comma (,) is the most common separator and for a TSV
            file, a tab is the most common separator. Selecting an incorrect 
            field separator will make the mapping in the next step impossible.
            If the data fields do not appear correctly in the next step, return
            to this step and check this selection. Consult the documentation
            of the program used to generate the data file to determine what
            separator character you should select. If in doubt, leave the
            default selection.
        </dd>
        <dt>
            Field Enclosure
        </dt>
        <dd>
            In order for a data field to contain the character used as the field
            separator and not be mistaken as a new field, the data field must be
            enclosed by another character so that the importer knows not to
            create a new field. For example, the tags field accepts a list of
            tags separated by a comma (,). In order to properly import the
            tags field, it needs to be enclosed by a character so that the
            commas (,) in the list are preserved. The default enclosure
            character is the double-quotation mark (&quot;) and so for our
            example, the tags field would appear in the data file as &quot;tag1,
            tag2, tag3, tag4&quot;. Consult the documentation of the program
            used to generate the data file to determine what enclosure character
            you should select. If in doubt, leave the default selection.
        </dd>
        <dt>
            Field Escaping
        </dt>
        <dd>
            In order for a data field to contain special characters, the
            special character needs to be immediately preceeded by an escape
            character. The escape character tells the importer to treat the
            next character as a literal rather than as say, the field enclosure
            or field separator. Consult the documentation of the program
            used to generate the data file to determine what enclosure character
            you should select. If in doubt, leave the default selection.
        </dd>
        <dt>
            Existing Records
        </dt>
        <dd>
            The external_id is used to form a unique key. 
            Therefore, only a single record for an arena with that external_id
            can exist. If the data file you are importing contains
            rows that will cause the unique key to be violated and the option
            to update existing records is set to No, then the import will fail
            with a unique constraint violation. By setting the update existing 
            records option to Yes, the importer will first check for any
            existing records that match the rows in the data file and will
            update them with the data in the data file and the import will
            complete. Please note that if the data file itself contains
            multiple rows for the same new record, the import will fail.
        </dd>
    </dl>
