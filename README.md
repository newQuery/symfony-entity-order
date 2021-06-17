# EntityOrderBundle

Helps to create ordered list from Entities.

# Usage

Add the trait to the desired entity

```php
namespace App\Entity\Misc;  
  
use App\Repository\Misc\TeamMemberRepository;  
use Doctrine\Common\Collections\ArrayCollection;  
use Doctrine\Common\Collections\Collection;  
use Doctrine\ORM\Mapping as ORM;  
use newQuery\Bundle\EntityOrder\Lib\OrderEntity;  
use Symfony\Component\Validator\Constraints as Assert;  
  
/**  
 * @ORM\Entity(repositoryClass=TeamMemberRepository::class)  
 * @ORM\HasLifecycleCallbacks()  
 */
 class TeamMember  
{  
  
  use OrderEntity;  // THIS 
  
  /**  
  * @ORM\Id  
  * @ORM\GeneratedValue  
  * @ORM\Column(type="integer")  
  */  
  private $id;
```

Make sure to add
`@ORM\HasLifecycleCallbacks() `

Enjoy, your entity now has the positionOrder property.

## Create a dynamic Controller for increasing/decreasing the position

```php
/**
 * @Route("/increase/{className}/{id}", name="increase_position")
 */
public function increase(string $className, int $id, OrderPositionHelper $helper, Request $request): Response
{
    $em = $this->getDoctrine()->getManager();
    $meta = $em->getMetadataFactory()->getAllMetadata();

    $repository = null;
    foreach ($meta as $m) {
        if(strpos($m->getName(), 'App\Entity') !== false && strpos($m->getName(), $className) !== false) {
            $repository = $em->getRepository($m->getName());
            break;
        }
    }

    if(null !== $repository) {
        $result = $helper->increase($repository, $id);
        $this->getDoctrine()->getManager()->flush();

        if(true === $result) {
            $this->addFlash('success', 'Ordre mis à jour!');
        }
    }

    return $this->redirect($request->headers->get('referer'));
}
```

```php
 /**
 * @Route("/decrease/{className}/{id}", name="decrease_position")
 */
public function decrease(string $className, int $id, OrderPositionHelper $helper, Request $request): Response
{
    $em = $this->getDoctrine()->getManager();
    $meta = $em->getMetadataFactory()->getAllMetadata();

    $repository = null;
    foreach ($meta as $m) {
        if(strpos($m->getName(), 'App\Entity') !== false && strpos($m->getName(), $className) !== false) {
            $repository = $em->getRepository($m->getName());
            break;
        }
    }

    if(null !== $repository) {
        $result = $helper->decrease($repository, $id);
        $this->getDoctrine()->getManager()->flush();

        if(true === $result) {
            $this->addFlash('success', 'Ordre mis à jour!');
        }
    }

    return $this->redirect($request->headers->get('referer'));
}
```

In twig template:
```twig
<a class="btn btn-primary" href="{{ path('decrease_position', {'id': member.id, 'className': 'TeamMember'}) }}">-</a>
<a class="btn btn-primary" href="{{ path('increase_position', {'id': member.id, 'className': 'TeamMember'}) }}">+</a>
```

## Error

You can fix the order of all element from one or all entities using the **trait** with
`$ php bin/console nq:position-order:fix <?Entity>`