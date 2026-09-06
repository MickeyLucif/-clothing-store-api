<?php

declare(strict_types=1);

namespace App\Enum;

enum Permission: string
{
    case VIEW_DASHBOARD = 'dashboard.view';

    case VIEW_ROLES = 'roles.view';
    case CREATE_ROLES = 'roles.create';
    case UPDATE_ROLES = 'roles.update';
    case DELETE_ROLES = 'roles.delete';

    case VIEW_PERMISSIONS = 'permissions.view';
    case CREATE_PERMISSIONS = 'permissions.create';
    case UPDATE_PERMISSIONS = 'permissions.update';
    case DELETE_PERMISSIONS = 'permissions.delete';

    case VIEW_CATEGORIES = 'categories.view';
    case CREATE_CATEGORIES = 'categories.create';
    case UPDATE_CATEGORIES = 'categories.update';
    case DELETE_CATEGORIES = 'categories.delete';

    case VIEW_PRODUCTS = 'products.view';
    case CREATE_PRODUCTS = 'products.create';
    case UPDATE_PRODUCTS = 'products.update';
    case DELETE_PRODUCTS = 'products.delete';

    case VIEW_ATTRIBUTES = 'attributes.view';
    case CREATE_ATTRIBUTES = 'attributes.create';
    case UPDATE_ATTRIBUTES = 'attributes.update';
    case DELETE_ATTRIBUTES = 'attributes.delete';

    case VIEW_ORDERS = 'orders.view';
    case CREATE_ORDERS = 'orders.create';
    case UPDATE_ORDERS = 'orders.update';
    case DELETE_ORDERS = 'orders.delete';

    case VIEW_USERS = 'users.view';
    case CREATE_USERS = 'users.create';
    case UPDATE_USERS = 'users.update';
    case DELETE_USERS = 'users.delete';

    case VIEW_PAYMENTS = 'payments.view';
    case VIEW_NOTIFICATIONS = 'notifications.view';
}
