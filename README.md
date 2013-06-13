# ResourceAccessBundle

## Instalation

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

### Step 3: Add your user class to doctrine's resolve_target_entities to override our requester class in config.yml:

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

namespace Acme/UserBundle/Entity/User

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

namespace Acme\YourBundle\Entity\MyResource

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
     * @ORM\OneToOne(targetEntity="AT\ResourceAccessBundle\Entity\Resource")
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

    /**
     * Here you construct your role hierarchy following this model.
     * You can have multiple hierarchies if needed but you must have a role that owns everything like our ROLE_ADMIN
     *
     * @return []
     */
    public function getRoleHierarchy()
    {
        $roleHierarchy = [
            'ROLE_ADMIN' => [
                'ROLE_EDIT_1' => [
                    'ROLE_READ_1'
                ],
                'ROLE_EDIT_2' => [
                    'ROLE_READ_2'
                ]
            ]
        ];

        return $roleHierarchy;
    }

    // ...
```

### Step 6: Update your schema with

``` bash
~ php app/console doctrine:schema:update --force
```

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