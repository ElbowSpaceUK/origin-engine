# Documentation Notes

## Adding Stubs

Stubs allow you to quickly scaffold repeated code in your app. Define the skeleton of the
code once, and the stub framework will help users customise the skeleton to their needs each time.

Stubs may also consist of multiple files or classes, letting you scaffold even quicker.

### Writing the stub file

A stub file is simply a file with some special syntax. For the most part, it'll look very similar to its output.
They don't need any particular name or extension, but we commonly end them with `.stub` to make it clear what they are.

Within a stub file, you may make use of variables given by the user to change properties of
outputted classes or files. You can also use control structures to only add certain properties, methods or sections of a file
when the user requests it.

#### Location
// TODO
Currently not supported anywhere but the cli, will be sorted asap

#### Printing data

To make use of PHP within your stub file, wrap code in double square brackets `[[ ]]` .
This will be executed when your stub is created, letting you change the output.

To print text to a file, use double curly brackets `[[{{ }}]]`. For example,

```
// model.php.stub
<?php

class [[{{ $modelName }}]] extends Model {}

// rendered to
<?php

class MyModel extends Model {}
```

You can also use standard php functions

```
// model.php.stub
<?php

class [[{{ $modelName }}]] extends Model {
protected $table = '[[{{ strtolower($modelName) }}]]s';
}

// rendered to
<?php

class MyModel extends Model {
protected $table = 'mymodels';
}
```

#### Control Structures

You may use any php control structures. For example, if sections

```
<?php
$val = 'one';
[[ if($someVariable === true) { ]]
$val = 'two';
[[ } ]]
echo "The answer is " . $val;
[[{{ $val }}]]

// Will compile to
<?php
$val = 'one';
$val = 'two'; // Only if $someVariable is true
echo "The answer is " . $val;
```

In a similar way, you can iterate through arrays using `foreach`.

### Registering a stub file

For your stub to be usable, you need to register it. Registration is normally done with the `\OriginEngine\Stubs\Stubs` class.

For now, this can be done in the stubs folder in this directory, and registered in the `AppServiceProvider`.
// TODO Where this should be done

#### The stub

This is the stub that users will interact with. It may consist of multiple files.

A stub takes the following options:
- Name: The name of the stub. This should be lower case and have no spaces (use `-` instead). This is how users will call on your stub.
- Description: A description of your stub.
- Default Location (optional): Where the stub should normally be saved. This is relative to the project directory,
so '`routes`' is the `routes` directory in your app. Leave blank to use the root of your app.

These can be registered using the `newStub` function on the `Stubs` class.

#### Files

Each file also needs to be registered. These take
- Stub path: The path to the stub file. Use `__DIR__` for the current directory.
- File name: The name the stub should be given when renamed.
- Relative location: The location of the outputted file relative to the stub default location.
- Show if: A callback which determines if the file should be shown or not. It takes an array of
data resolved from the user, and should return a boolean. By default, all files are used.

This uses the `newStubFile` function to register it.

The file name may be dynamic, letting you ask the user for information before deciding what the file name should be. If you
would like to make use of this feature, register a function as the file name. This function accepts an array of data, and
should return the filename.

```php
public function boot(\OriginEngine\Stubs\Stubs $stubs)
{
    $stubs->newStub(...)
        ->addFile(
            $stubs->newStubFile(
                __DIR__ . '/../stubs/ModelController.php.stub', fn($data) => sprintf('%sController.php', $data['modelName']), 'app/Models'
            )
                ->addReplacement($stubs->newStringReplacement('modelName', 'What is the name of the model?'))
        );
}
```

#### Replacements

Each variable you use within a file also needs to be registered. All variables take the following:
- Variable Name: The name of the variable, to use in the stubs file. This must be unique between all stub files being registered, and will always
be passed to all stub files generated in the stub.
- Question text: The text to show to the user when prompting for the value
- Default: The default value to use.
- Validator: A function that takes the value, and can return true or false if the value is valid or not

##### Boolean
Created with the `newBooleanReplacement` function, is used for asking the user yes/no questions.

##### String
Created with the `newStringReplacement` function, is used for asking the user text based questions.

##### Array
Created with the `newArrayReplacement` function, is used for asking the user for an array of answers.

Alongside the defaults, the array replacement also takes a different replacement. It will ask this replacement
as many times as the user wants to build up an array.

##### Section
Created with the `newSectionReplacement` function, is used for making sections of stubs conditional.

It will create a boolean variable which you can use control structures on, and accepts an
array of replacements to ask only if the user wants the section.

##### Database Schema
This is a special type, created with the `newTableColumnReplacement` variable. It will ask the user for a name for the
column, a type for the column, and whether the column can be null. This is then passed as an instance of `\OriginEngine\Stubs\Replacements\TableColumn` to the stub files.
