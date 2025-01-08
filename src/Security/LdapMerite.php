<?php

namespace AcMarche\MeriteSportif\Security;

use Symfony\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\Ldap\Adapter\EntryManagerInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Exception\DriverNotFoundException;
use Symfony\Component\Ldap\LdapInterface;

/**
 * Copy/Paste.
 *
 * @see Ldap
 */
class LdapMerite implements LdapInterface
{
    public function __construct(
        private AdapterInterface $adapter,
    ) {}

    public function bind(?string $dn = null, #[\SensitiveParameter] ?string $password = null): void
    {
        $this->adapter->getConnection()->bind($dn, $password);
    }

    public function saslBind(
        ?string $dn = null,
        #[\SensitiveParameter]
        ?string $password = null,
        ?string $mech = null,
        ?string $realm = null,
        ?string $authcId = null,
        ?string $authzId = null,
        ?string $props = null,
    ): void {
        $this->adapter->getConnection()->saslBind($dn, $password, $mech, $realm, $authcId, $authzId, $props);
    }

    public function whoami(): string
    {
        return $this->adapter->getConnection()->whoami();
    }

    public function query(string $dn, string $query, array $options = []): QueryInterface
    {
        return $this->adapter->createQuery($dn, $query, $options);
    }

    public function getEntryManager(): EntryManagerInterface
    {
        return $this->adapter->getEntryManager();
    }

    public function escape(string $subject, string $ignore = '', int $flags = 0): string
    {
        return $this->adapter->escape($subject, $ignore, $flags);
    }

    /**
     * Creates a new Ldap instance.
     *
     * @param string $adapter The adapter name
     * @param array $config The adapter's configuration
     */
    public static function create(string $adapter, array $config = []): static
    {
        if ('ext_ldap' !== $adapter) {
            throw new DriverNotFoundException(
                \sprintf('Adapter "%s" not found. Only "ext_ldap" is supported at the moment.', $adapter),
            );
        }

        return new self(new Adapter($config));
    }
}
