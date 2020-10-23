This is no OR-mapping functionality, therefore the schema on the database has to be created manually.

**Entities**

The entities (the data objects) are simple PHP classes. They must have an `$id` field and a constructor accepting all fields is needed. To be able to serialize them as JSON objects, they have to implement the `JsonSerializable` in the interface. In the `jsonSerialize` method, all the object properties are returned via the `get_object_vars` method.

```php
class User implements JsonSerializable {
    private $id;
    private $firstName;
    private $lastName;
    
    function __construct($id, $firstName, $lastName) {
        $this->id = $id;
        $this->firstName = $firstName;
        ...
    }
    
    public function getId() { ... }
    public function setId($id) { ... }
    // all getters / setters for the other properties
    
    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}
```

**Data Access Objects**

For every entity, there is a data access object. It derives from `GenericDao` and creates the basic CRUD operations using reflection. The SQL queries are created dynamically and are then processed via plain PDO. 

For example, to find all entities, a simple `SELECT * FROM ` query is created and the result is automatically mapped to an entity, so that, for example, instances of the class `User` are returned. 

Therefore a DAO class has to be created that overrides the `getEntityClass` method:

```php
class UserDao extends GenericDao {
    protected function getEntityClass() {
        return 'User';
    }
}
```

The `GenericDao` then creates the query 

```php
public function findAll() {
    $query = "SELECT * FROM ".strtolower($this->getEntityClass()).";";
    $stmt = $this->dbh->prepare($query);
    if ($stmt->execute()) {
        $result = [];
        while ($row = $stmt->fetch()) {
            array_push($result, $this->mapToEntity($row));
        }
        return $result;
    }
    return [];
}
```

The other methods for create, update and delete work in a similar way. For create and update also the fields of the entity are needed. This is done using the `ReflectionClass` class. 

```php
$this->reflection = new ReflectionClass($this->getEntityClass());
$properties = $this->reflection->getProperties(ReflectionProperty::IS_PRIVATE);
return $properties;
```

**Controller**

For business logic a service layer would be inserted between DAOs and controllers, but for sake of simplicity I left i for now. It may be added in the future.

For the controllers a main class exists: `EntityController`. This class takes an instance of a concrete DAO implementation and has one function: `handleRequests`.

In this method the CRUD operations are defined:

- Request method `GET` and url parameter `?findAll` calls `dao->findAll`
- Request method `GET` and url parameter `?findById=1` returns the entity with id = 1 (by calling `dao->findById`)
- Request method `PUT` and url parameter `?save` calls `dao->insert`. The entity has to be sent in the request body as json object.
- Request method `PUT` and url parameter `?update` calls `dao->update`. Again, the entity has to be in the request body.
- Request method `DELETE` and url parameter  `?delete=1`  calls `dao->delete` and deletes the entity with id 1.

Continuing the `User` example, the controller would look like this: 

```php
header('Content-type: application/json');
$controller = new EntityController(new EventDao());
$controller->handleRequests();
```

The controller can then be called by url like this: `http://www.mydomain.com/api/userController.php?findAll`

**Summary**

Of course this is not the most secure, powerful or even efficient way to do it, but it provides you with the basic CRUD operations out of the box so that you can focus on the user interface and your business logic without having to waste time on this standard stuff.