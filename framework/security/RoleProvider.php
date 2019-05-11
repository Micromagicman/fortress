<?php

namespace fortress\security;

interface RoleProvider {
    public function getRoleMap();
}