<?php
    /* @var $fields array[][] */
?>

    <h3>
        Tips
    </h3>
    <dl>
        <dt>
            Data File Headers
        </dt>
        <dd>
            Whenever possible, the first row in your data file should be a
            header row with the field names matching exactly the name of the
            fields in this table.
        </dd>
        <dt>
            Data File Fields
        </dt>
        <dd>
            Whenever possible, should exactly match the expected type and size
            of data for the database field. This will greatly reduce any
            import errors
        </dd>
    </dl>
    <h3>
        Field Types
    </h3>
    <dl>
        <dt>
            string
        </dt>
        <dd>
            The <strong>string</strong> data type consists of character data
            and the <strong>size</strong> of the field refers to the number of
            characters in the string. Character data that exceeds the size will
            be truncated.
        </dd>
        <dt>
            text
        </dt>
        <dd>
            The <strong>text</strong> data type consists of character data
            and the <strong>size</strong> of the field is unlimited. Performance
            can be greatly reduced if an excessive amount of data is imported to
            fields of this type. 
        </dd>
        <dt>
            integer
        </dt>
        <dd>
            The <strong>integer</strong> data type consists of numeric data. It
            is a whole number, that is it does not have a fractional part. Any
            fractional parts specified are truncated. An optional sign may
            preceed the number.
        </dd>
        <dt>
            float
        </dt>
        <dd>
            The <strong>float</strong> data type consists of numeric data. It
            is a real number, that is it may have a fractional part. An 
            optional sign may preceed the number.
        </dd>
        <dt>
            datetime
        </dt>
        <dd>
            The <strong>datetime</strong> data type consists of both date and
            time data. If either the date part or time part is missing, the
            system will provide a value for the missing part. Internally, the
            data is stored in this format: 2014-01-31 19:59:59, but, the system
            is able to understand and convert the most common U.S. datetime
            formats such as January 31, 2014 7:59:59 PM.
        </dd>
        <dt>
            date
        </dt>
        <dd>
            The <strong>date</strong> data type consists of only the date
            data. If a portion of the date part is missing, the
            system will provide a value for the missing part. Internally, the
            data is stored in this format: 2014-01-31, but, the system
            is able to understand and convert the most common U.S. date
            formats such as January 31, 2014.
        </dd>
        <dt>
            time
        </dt>
        <dd>
            The <strong>time</strong> data type consists of time data. If a 
            portion of the time part is missing, the system will provide a value
            for the missing part. Internally, the data is stored in this format:
            19:59:59, but, the system is able to understand and convert the most
            common U.S. time formats such as 7:59:59 PM.
        </dd>
        <dt>
            phone
        </dt>
        <dd>
            The <strong>phone</strong> data type consists of character data that
            is stripped of all non numeric characters. The size specifies the
            maximum number of digits allowed in the number.
        </dd>
    </dl>
    <h3>
        Required Fields
    </h3>
    <dl>
        <?php foreach($fields as $field) : ?>
            <?php if($field['required'] == true) : ?>
            <dt>
                <h4  class="text-error">
                    <?php echo $field['name'] ?>
                </h4>
            </dt>
            <dd>
                <dl class="dl-horizontal">
                    <dt>
                        Friendly Name:
                    </dt>
                    <dd>
                        <?php echo $field['display'] ?>
                    </dd>
                    <dt>
                        Type:
                    </dt>
                    <dd>
                        <?php echo $field['type'] ?>
                    </dd>
                    <dt>
                        Size:
                    </dt>
                    <dd>
                        <?php 
                            if($field['size'] > 0) {
                                echo $field['size'];
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </dd>
                    <dt>
                        Description:
                    </dt>
                    <dd>
                        <?php echo $field['tooltip'] ?>
                    </dd>
                    <dt>
                        Example:
                    </dt>
                    <dd>
                        <?php echo $field['example'] ?>
                    </dd>
                </dl>
            </dd>
            <?php endif; ?>
        <?php endforeach; ?>
    </dl>
    <h3>
        Optional Fields
    </h3>
    <dl>
        <?php foreach($fields as $field) : ?>
            <?php if($field['required'] == false) : ?>
            <dt>
                <h4>
                    <?php echo $field['name'] ?>
                </h4>
            </dt>
            <dd>
                <dl class="dl-horizontal">
                    <dt>
                        Friendly Name:
                    </dt>
                    <dd>
                        <?php echo $field['display'] ?>
                    </dd>
                    <dt>
                        Type:
                    </dt>
                    <dd>
                        <?php echo $field['type'] ?>
                    </dd>
                    <dt>
                        Size:
                    </dt>
                    <dd>
                        <?php 
                            if($field['size'] > 0) {
                                echo $field['size'];
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </dd>
                    <dt>
                        Description:
                    </dt>
                    <dd>
                        <?php echo $field['tooltip'] ?>
                    </dd>
                    <dt>
                        Example:
                    </dt>
                    <dd>
                        <?php echo $field['example'] ?>
                    </dd>
                </dl>
            </dd>
            <?php endif; ?>
        <?php endforeach; ?>
    </dl>
