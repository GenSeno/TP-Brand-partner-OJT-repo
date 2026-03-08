import { usePage } from '@inertiajs/vue3';

/**
 * Check if the current user has a specific permission
 * @param {string} permission - Permission name to check
 * @returns {boolean}
 */
export function can(permission) {
    const page = usePage();

    if (page.props.auth.user.admin) return true;

    const permissions = page.props.auth?.permissions;

    if (permissions.length === 0) return false;

    return permissions.includes(permission);
}

/**
 * Check if the current user has any of the given permissions
 * @param {string[]} permissions - Permission names to check
 * @returns {boolean}
 */
export function hasAnyPermission(permissions = []) {
    if (permissions.length === 0) return true;

    const page = usePage();

    if (page.props.auth.user.admin) return true;

    const userPermissions = page.props.auth?.permissions;

    if (!userPermissions.length === 0) return false;

    return permissions.some((permission) =>
        userPermissions.includes(permission),
    );
}

/**
 * Check if the current user has all of the given permissions
 * @param {string[]} permissions - Permission names to check
 * @returns {boolean}
 */
export function hasAllPermissions(permissions = []) {
    if (permissions.length === 0) return true;

    const page = usePage();

    if (page.props.auth.user.admin) return true;

    const userPermissions = page.props.auth?.permissions;

    if (userPermissions.length === 0) return false;

    return permissions.every((permission) =>
        userPermissions.includes(permission),
    );
}

/**
 * Check if the current user has a specific role
 * @param {string} role - Role name to check
 * @returns {boolean}
 */
export function hasRole(role) {
    const page = usePage();
    const roles = page.props.auth?.roles;

    if (!roles) {
        if (role === 'Administrator' && page.props.auth.user.admin) {
            return true;
        }
        return false;
    }

    return roles.includes(role);
}

/**
 * Check if the current user has any of the given roles
 * @param {...string} roles - Role names to check
 * @returns {boolean}
 */
export function hasAnyRole(...roles) {
    const page = usePage();
    const userRoles = page.props.auth?.roles;

    if (!userRoles) {
        if (roles.includes('Administrator') && page.props.auth.user.admin) {
            return true;
        }
        return false;
    }

    return roles.some((role) => userRoles.includes(role));
}

/**
 * Check if the current user has all of the given roles
 * @param {...string} roles - Role names to check
 * @returns {boolean}
 */
export function hasAllRoles(...roles) {
    const page = usePage();
    let userRoles = page.props.auth?.roles;

    if (page.props.auth.user.admin) {
        userRoles = ['Administrator', ...userRoles];
    }

    if (!userRoles) return false;

    return roles.every((role) => userRoles.includes(role));
}
