    <h3>
      Important Information
    </h3>
    <dl>
        <dt>
            Before You Begin
        </dt>
        <dd>
            Before you attempt to import your first file using this importer,
            please open the <strong>Field Information</strong> dialog to
            determine what type of data to include in your import file.
        </dd>
        <dt>
            Mac Users
        </dt>
        <dd>
            If the file you are importing was created using an Apple Mac
            computer and you are having problems importing it, please ensure
            that the file was saved using MS-DOS / MS-Windows line endings.
        </dd>
        <dt>
            Field Header Row
        </dt>
        <dd>
            Although not absolutely required, if your data file does not have a
            header row, then the first row to be imported will be used as the
            header and will not itself be imported. Automatic field mapping will
            also likely not work in this case.
        </dd>
        <dt>
            Existing Records
        </dt>
        <dd>
            The arena name, city, and state are used to form a unique key. 
            Therefore, only a single record for an arena name, city, and state
            combination can exist. If the data file you are importing contains
            rows that will cause the unique key to be violated and the option
            to update existing records is set to No, then the import will fail
            with a unique constraint violation. By setting the update existing 
            records option to Yes, the importer will first check for any
            existing records that match the rows in the data file and will
            update them with the data in the data file and the import will
            complete. Please note that if the data file itself contains
            multiple rows for the same new record, the import will fail.
        </dd>
        <dt>
            Data File Types
        </dt>
        <dd>
            The importer can import virtually any text based data file, however;
            on the <strong>Select File</strong> dialog, you can only select
            files with a csv, tsv, or txt extension.
        </dd>
    </dl>
    <h3>
      Importer Features
    </h3>
    <dl>
        <dt>
            Auto Tagging
        </dt>
        <dd>
            Imported records will automatically be tagged with the name, city,
            and state. You may also provide your own tags and they will be
            processed correctly in addition to the automatic tags.
        </dd>
        <dt>
            Map To Multiple Fields
        </dt>
        <dd>
            You may map a data file field to multiple table fields.
        </dd>
        <dt>
            Automatic Field Mappings
        </dt>
        <dd>
            A field will automatically be mapped if the table field name appears
            in the data file header row.
        </dd>
        <dt>
            Data Type Conversion
        </dt>
        <dd>
            The importer will attempt to auto detect and convert certain data
            types before importing the data in to the database. For example, if
            there is a date field and the data file contains November 25, 2013
            as the value, it will be converted to 2013-11-25 before being
            stored in the database.
        </dd>
        <dt>
            Field Truncation
        </dt>
        <dd>
            Data in the file that exceeds the size of the database field will be
            truncated. This means that if a field in the data file contains ten
            characters and it is mapped to a database field that only holds five
            characters, then only the first five characters in the data file
            will be imported.
        </dd>
        <dt>
            Field Stripping
        </dt>
        <dd>
            Any characters in a data file field that do not conform to the
            database field type will be stripped before being imported. For
            example, the phone number field only holds the ten digit phone
            number, therefore all non-numeric characters such as () and - will
            be stripped from the data before being imported.
        </dd>
    </dl>
    <h3>
      Performing an Import
    </h3>
    <dl>
        <dt>
            Step 1
        </dt>
        <dd>
            Begin by uploading your data file to the server.
            <ol>
                <li>
                    Click the <strong>Select File</strong> button and select
                    your import file. Keep in mind that it must have a <strong>
                    csv, tsv, or txt</strong> extension.
                </li>
                <li>
                    Click the <strong>Begin Upload</strong> button to send your
                    file to the server. If the transfer is successful, you will
                    be brought to step 2.
                </li>
                <li>
                    If an error happens, you will be shown an error dialog with
                    details about the error that occurred. You can close the
                    error dialog and retry the upload. If the error persists, 
                    copy the error information in to an e-mail and send it off
                    to your Application Administrator.
                </li>
            </ol>
        </dd>
        <dt>
            Step 2
        </dt>
        <dd>
            Set your import settings.
            <ol>
                <li>
                    Click the <strong>Instructions</strong> button to review
                    detailed information on how to set the settings for this step.
                </li>
                <li>
                    Once you have selected your options, click the <strong>
                    Continue</strong> button to proceed to step 3.
                </li>
                <li>
                    If an error happens, you will be shown an error dialog with
                    details about the error that occurred. You can close the
                    error dialog and make another attempt to continue. If the
                    error persists, copy the error information in to an e-mail
                    and send it off to your Application Administrator.
                </li>
            </ol>
        </dd>
        <dt>
            Step 3
        </dt>
        <dd>
            Create the table and data file mappings.
            <ol>
                <li>
                    Click the <strong>Instructions</strong> button to review
                    detailed information on how to set the settings for this step.
                </li>
                <li>
                    Once you have selected your options, click the <strong>
                    Import</strong> button to import the uploaded data file in
                    to the database. If the import succeeds, you will be brought
                    to the summary screen.
                </li>
                <li>
                    If an error happens, you will be shown an error dialog with
                    details about the error that occurred. You can close the
                    error dialog and make another attempt to continue. If the
                    error persists, copy the error information in to an e-mail
                    and send it off to your Application Administrator.
                </li>
            </ol>
        </dd>
        <dt>
            Summary Screen
        </dt>
        <dd>
            The summary screen will display the results from the import. After
            reviewing the results, you may import another file if you wish.
        </dd>
    </dl>
