# ResourceAccessBundle

## Instalation

1. Add "at/resource-access": "dev-master" to your composer.json:

    {
        "require": {
            "at/resource-access": "dev-master"
        }
    }

2. Run 'php composer.phar update at/resource-access'

3. Add 'new AT\ResourceAccessBundle\ATResourceAccessBundle()', to your AppKernel.php:

    public function registerBundles()
    {
        $bundles = array(
            new AT\ResourceAccessBundle\ATResourceAccessBundle(),
        )
    }

4. Add your user class to doctrine's resolve_target_entities to override our requester class in config.yml:

    doctrine:
        orm:
            resolve_target_entities:
                AT\ResourceAccessBundle\Entity\Requester: Acme\UserBundle\Entity\User

5. Make the user class implement RequesterInterface:

    <?php

        namespace Acme/UserBundle/Entity/User

        use AT\ResourceAccessBundle\Model\RequesterInterface;

        class User implements RequesterInterface
        {
            // your content here
        }

5. Create your resource entity that implements ResourceInterface and add the mappings to our Resource class like this:

    <?php

        namespace Acme\YourBundle\Entity\MyResource

        use AT\ResourceAccessBundle\Entity\Resource;
        use AT\ResourceAccessBundle\Model\ResourceInterface;

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

            public function getResource()
            {
                return $this->resource;
            }

            /**
             * Here you construct your role hierarchy following this model.
             * You can have multiple hierarchies if needed but you must have a role that owns everything like our ROLE_ADMIN
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

6. Update your schema with :

    php app/console doctrine:schema:update --force

7. You can now use the manager to manage the roles on your resource:

    //DefaultController.php
    ...
    $resourceAccessManager = $this->get('resource_access_manager');

    /*
     * Grants access to user for specified resource.
     * $grantedBy is optional
     */
    $resourceAccessManager->grantAccess($user, $resource, ['array', 'of', 'accesses'], $grantedBy);

    /*
     * Returns true if user has specified access for resource, otherwise returns false
     */
    $resourceAccessManager->isGranted($access, $resource, $user);

    /*
     * Replaces whatever accesses the user has with the provided ones
     * $grantedBy is optional
     */
    $resourceAccessManager->updateAccessLevels($user, $resource, ['array', 'of', 'accesses'], $grantedBy);

    /*
     * Remove specified user accesses for specified resource
     */
    $resourceAccessManager->removeAccessLevels($user, $resource, ['array', 'of', 'accesses', 'to', 'be', 'removed']);

    /*
     * Removes all user accesses for specified resource
     */
    $resourceAccessManager->removeAccess($user, $resource);