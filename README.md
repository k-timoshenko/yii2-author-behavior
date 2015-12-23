# Author behavoir

Behavior represents additional functionality similar to yii\db\TimestampBehavior
  but automatically fills the specified attributes with the current user id 
  or value defined in `value` field.

## Install

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require --prefer-dist t-kanstantsin/yii2-author-behavior "*"
```

or add

```json
"t-kanstantsin/yii2-author-behavior": "*"
```

to the require section of your `composer.json` file.


## Usage

Example behavior configuration:

```php
    // in `behaviors` method
    [
        'class' => tkanstantsin\yii2\behaviors\AuthorBehavior::class, 
        'createdByAttribute' => 'created_by_id',
        'updatedByAttribute' => 'updated_by_id',
    ],
```

## Credits

- [Konstantin Timoshenko](https://github.com/t-kanstantsin)

## License

The BSD License (BSD). Please see [License File](LICENSE.md) for more information.
