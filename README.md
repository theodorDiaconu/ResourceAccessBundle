# ResourceAccessBundle

## Installation

### Step 1: Download ResourceAccessBundle using composer

Add ResourceAccessBundle in your composer.json:

``` js
{
    "require": {
        "at/resource-access": "dev-master"
    }
}
```

Now download the bundle by running the command:

``` bash
~ php composer.phar update at/resource-access
```

Composer will install the bundle to your project's `vendor/at` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new AT\ResourceAccessBundle\ATResourceAccessBundle(),
    );
}
```

### Step 3: Add your user class to doctrine's resolve_target_entities in config.yml:

``` yaml
    doctrine:
        orm:
            resolve_target_entities:
                AT\ResourceAccessBundle\Entity\Requester: Acme\UserBundle\Entity\User
```

### Step 4: Make the user class implement RequesterInterface:

``` php
<?php
// src/Acme/UserBundle/Entity/User.php

namespace Acme/UserBundle/Entity

// ...
use AT\ResourceAccessBundle\Model\RequesterInterface;

class User implements RequesterInterface
{
    // your content here
}
```

### Step 5: Create your resource entity that implements ResourceInterface and add the mappings to our Resource class like this:

``` php
<?php
// src/Acme/YourBundle/Entity/MyResource.php

namespace Acme\YourBundle\Entity\

use Doctrine\ORM\Mapping as ORM;
use AT\ResourceAccessBundle\Entity\Resource;
use AT\ResourceAccessBundle\Model\ResourceInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="my_resources")
 */
class MyResource implements ResourceInterface
{
    // ...

    /**
     * @ORM\OneToOne(targetEntity="AT\ResourceAccessBundle\Entity\Resource", cascade={"persist"})
     */
    protected $resource;

    // ...

    public function __construct()
    {
        $this->resource  = new Resource();
    }

    // ...

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    // ...
```

### Step 6: Update your schema with

``` bash
~ php app/console doctrine:schema:update --force
```

### Step 7: Define your role hierarchy for each resource class in config.yml like this:

``` yaml
at_resource_access:
    resources:
        Acme/YourBundle/Entity/MyResource:
            role_hierarchy:
                ROLE_ADMIN: [ ROLE_EDIT ]
                ROLE_EDIT:  [ ROLE_READ ]
```

##### Note
The first role ( in this case ROLE_ADMIN ) will be considered the master role and it will have access over any other role defined.
If you would add another parent role like this :

``` yaml
Acme/YourBundle/Entity/MyResource:
    role_hierarchy:
        ROLE_ADMIN:     [ ROLE_EDIT ]
        ROLE_EDIT:      [ ROLE_READ ]
        ROLE_REVIEW:    [ ROLE_READ_REVIEW, ROLE_EDIT_REVIEW ]
```

The ROLE_REVIEW will be considered a child of ROLE_ADMIN even though you didn't define it like this,
so always make sure your roles are related between them to eliminate any possible confusions.

## How to use the ResourceAccessBundle

You can now use the manager to manage the roles on your resource:

``` php
<?php
// src/Acme/YourBundle/Controller/MyResourceController.php

    // ...
    $resourceAccessManager = $this->get('resource_access_manager');

    /**
     * Grants access to user for specified resource.
     * $grantedBy is optional
     */
    $resourceAccessManager->grantAccess($user, $resource, ['array', 'of', 'accesses'], $grantedBy);

    /**
     * Returns true if user has specified access for resource, otherwise returns false
     * $user is optional
     * If $user is not provided the method will use the logged in user from security.context
     */
    $resourceAccessManager->isGranted($access, $resource, $user);

    /**
     * Replaces whatever accesses the user has with the provided ones
     * $grantedBy is optional
     */
    $resourceAccessManager->updateAccessLevels($user, $resource, ['array', 'of', 'accesses'], $grantedBy);

    /**
     * Remove specified user accesses for specified resource
     */
    $resourceAccessManager->removeAccessLevels($user, $resource, ['array', 'of', 'accesses', 'to', 'be', 'removed']);

    /**
     * Removes all user accesses for specified resource
     */
    $resourceAccessManager->removeAccess($user, $resource);
```

## Testing

For the tests to run you have to add these lines in your config_test.yml

``` yaml

    doctrine:
        orm:
            resolve_target_entities:
                AT\ResourceAccessBundle\Entity\Requester: AT\ResourceAccessBundle\Tests\Entity\Requester
            mappings:
                requester:
                    type: annotation
                    dir: %kernel.root_dir%/../vendor/at/resource-access/AT/ResourceAccessBundle/Tests/Entity
                    alias: Requester
                    prefix: AT\ResourceAccessBundle\Tests\Entity
                    is_bundle: false
```

Now run this command to run the tests:

``` bash
~ phpunit -c app vendor/at/resource-access/AT/ResourceAccessBundle/Tests
```
