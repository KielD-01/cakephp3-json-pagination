# CakePHP 3.x JSON Pagination Trait

## Badges

[![Github All Releases](https://img.shields.io/github/downloads/KielD-01/cakephp3-json-pagination/total.svg?style=plastic)](https://github.com/KielD-01/cakephp3-json-pagination) ![Packagist](https://img.shields.io/packagist/dt/kield-01/cakephp3-json-pagination.svg)

## Installation
`composer require kield-01/cakephp3-json-pagination && composer dump-autoload -o`

## How to use?

In Your `AppController`, You must add `use JsonPaginationTrait` to use the trait application-wide:

````
class AppController extends Controller
{

    use JsonPaginationTrait; 
}
````

Or You could add it only to the controller You want:

````
class UsersController extends Controller
{

    use JsonPaginationTrait; 
}
````

## How to get the data?

At first, You should load Your model, which You want to use with `JsonPaginationTrait` or to use autoloaded by the classname:

````
class UsersController extends Controller
{

    use JsonPaginationTrait; 
    
    public function index()
    {
        /** If You want to use custom alias, You should pass second argument **/
        return $this->j_paginate($this->Users->find(), $this->Users->getTable());
        
        /** Regular response with classic data alias **/
        return $this->j_paginate($this->Users->find());
    }

    
}
````