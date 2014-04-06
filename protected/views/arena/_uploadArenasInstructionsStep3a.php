    <dl>
        <dt>
            What Fields to Map
        </dt>
        <dd>
            Fields with a <span class="required">*</span> are required to be mapped.
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
            A field will automatically be mapped if the table field name appears in
            the data file header row.
        </dd>
        <dt>
            Table Column
        </dt>
        <dd>
            Displays the column names that can be populated through the import process.
            Placing your cursor over a field in this column will display a tool-tip
            that will display information about the field such as its size and type.
        </dd>
        <dt>
            Data File Column
        </dt>
        <dd>
            Use the drop-down lists in this column to map fields in the data file to
            fields in the table. If the selection list is blank or does not contain
            what you expect, return to the previous step and double check the selections
            for field separator, field enclosure, and field escape.
        </dd>
        <dt>
            Data File Example
        </dt>
        <dd>
            Displays the file field data as it exists in the file for the currently
            mapped data file field. This column will automatically update based on the
            the selection in the Data File Column. It does not display the data as it
            will be stored in the database. In other words, It displays the data without
            stripping and without conversion.
        </dd>
        <dt>
            Field Size
        </dt>
        <dd>
            Data in the file that exceeds the size of the database field will be truncated.
            If the tool-tip does not explicitly state a size or length, then the size is
            based on the type of data that the field accepts.
        </dd>
        <dt>
            Field Type
        </dt>
        <dd>
            Any characters in the data from the file that do not conform to the database field type
            will be stripped before being imported. For example, for the phone number field, all
            formatting characters such as () and - will be removed before being imported. If the
            tool-tip does not explicitly state a field type, then the type is implied by the name
            of the field. For example, the &quot;lat&quot; field type is implied to be a floating
            point number as that is how lattitude is specified.
        </dd>
        <dt>
            Data Type Conversion
        </dt>
        <dd>
            The importer will attempt to auto detect and convert certain data types before importing
            the data in to the database. For example, if there is a date field and the data file
            contains November 25, 2013 as the value, it will be converted to 2013-11-25 before being
            stored in the database.
        </dd>
        <dt>
            Auto Tagging
        </dt>
        <dd>
            Imported records will automatically be tagged with the name, city, and state. You may also
            provide your own tags and they will be processed correctly in addition to the automatic tags.
        </dd>
        <dt>
            How to Continue
        </dt>
        <dd>
            You will not be able to continue until all required <span class="required">*</span>
            table fields have been mapped to a data file field.
        </dd>
    </dl>
