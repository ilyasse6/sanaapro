function toggleNode(nodeId) {
    const children = document.getElementById(nodeId + "_children");
    const icon = document.getElementById("icon_" + nodeId);

    if (children && icon) {
        if (children.classList.contains("show")) {
            children.classList.remove("show");
            icon.classList.remove("bi-chevron-down");
            icon.classList.add("bi-chevron-right");
        } else {
            children.classList.add("show");
            icon.classList.remove("bi-chevron-right");
            icon.classList.add("bi-chevron-down");
        }
    }
}

function toggleAllPermissions(checkbox) {
    const allCheckboxes = document.querySelectorAll(
        ".permission-checkbox, .entreprise-checkbox, .module-checkbox"
    );

    allCheckboxes.forEach((cb) => {
        if (!cb.disabled) {
            cb.checked = checkbox.checked;
        }
    });

    if (checkbox.checked) {
        clearAllFlags();
        addGlobalPermissionFlag();
    } else {
        clearAllFlags();
    }

    updateAllCheckboxStates();
}

function addGlobalPermissionFlag() {
    clearAllFlags();
    const globalFlag = document.createElement("input");
    globalFlag.type = "hidden";
    globalFlag.name = "permission_type";
    globalFlag.value = "global";
    globalFlag.id = "permission_type_flag";
    document.getElementById("profilForm").appendChild(globalFlag);
}

function toggleEntreprisePermissions(entrepriseId, checkbox) {
    const entrepriseElements = document.querySelectorAll(
        `[data-entreprise-id="${entrepriseId}"]`
    );

    entrepriseElements.forEach((element) => {
        if (
            element !== checkbox &&
            !element.classList.contains("entreprise-checkbox") &&
            !element.disabled
        ) {
            element.checked = checkbox.checked;
        }
    });

    updatePermissionFlags();
    updateParentCheckboxes(checkbox);
}

function toggleModulePermissions(entrepriseId, moduleId, checkbox) {
    const modulePermissions = document.querySelectorAll(
        `[data-module-id="${moduleId}"][data-entreprise-id="${entrepriseId}"].permission-checkbox`
    );

    modulePermissions.forEach((cb) => {
        if (!cb.disabled) {
            cb.checked = checkbox.checked;
        }
    });

    updatePermissionFlags();
    updateParentCheckboxes(checkbox);
}

function isAllPermissionsSelected() {
    const allCheckboxes = document.querySelectorAll(
        ".permission-checkbox, .entreprise-checkbox, .module-checkbox"
    );
    const activeCheckboxes = Array.from(allCheckboxes).filter(
        (cb) => !cb.disabled
    );
    const checkedCheckboxes = activeCheckboxes.filter((cb) => cb.checked);
    return (
        checkedCheckboxes.length === activeCheckboxes.length &&
        activeCheckboxes.length > 0
    );
}

function isWholeEntrepriseSelected(entrepriseId) {
    const entrepriseCheckbox = document.getElementById(
        `entreprise_${entrepriseId}`
    );
    if (entrepriseCheckbox && entrepriseCheckbox.checked) {
        return true;
    }

    const entrepriseElements = document.querySelectorAll(
        `[data-entreprise-id="${entrepriseId}"]`
    );
    const activeElements = Array.from(entrepriseElements).filter(
        (el) =>
            !el.disabled &&
            !el.classList.contains("entreprise-checkbox") &&
            (el.classList.contains("permission-checkbox") ||
                el.classList.contains("module-checkbox"))
    );
    const checkedElements = activeElements.filter((el) => el.checked);

    return (
        activeElements.length > 0 &&
        checkedElements.length === activeElements.length
    );
}

function clearAllFlags() {
    const existingFlags = document.querySelectorAll(
        '#profilForm input[type="hidden"]'
    );
    existingFlags.forEach((flag) => {
        if (flag.id !== "csrf_token" && flag.name !== "_token") {
            flag.remove();
        }
    });
}

function updatePermissionFlags() {
    clearAllFlags();

    if (isAllPermissionsSelected()) {
        addGlobalPermissionFlag();
        return;
    }

    const selectedEntreprises = [];

    document.querySelectorAll(".entreprise-checkbox").forEach((cb) => {
        const entrepriseId = cb.dataset.entrepriseId;
        if (entrepriseId && isWholeEntrepriseSelected(entrepriseId)) {
            selectedEntreprises.push(entrepriseId);
        }
    });

    selectedEntreprises.forEach((id) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "selected_entreprises[]";
        input.value = id;
        document.getElementById("profilForm").appendChild(input);
    });

    addSpecificPermissionFlags();
}

function addSpecificPermissionFlags() {
    const permissionCheckboxes = document.querySelectorAll(
        ".permission-checkbox:checked"
    );

    let index = 0;
    permissionCheckboxes.forEach((checkbox) => {
        const entrepriseId = checkbox.dataset.entrepriseId;
        if (isWholeEntrepriseSelected(entrepriseId)) {
            return;
        }

        const permFlag = document.createElement("input");
        permFlag.type = "hidden";
        permFlag.name = `specific_permissions[${index}][permission_id]`;
        permFlag.value = checkbox.dataset.permissionId;
        document.getElementById("profilForm").appendChild(permFlag);

        const entrepriseFlag = document.createElement("input");
        entrepriseFlag.type = "hidden";
        entrepriseFlag.name = `specific_permissions[${index}][entreprise_id]`;
        entrepriseFlag.value = entrepriseId;
        document.getElementById("profilForm").appendChild(entrepriseFlag);

        index++;
    });
}

function updateParentCheckboxes(changedCheckbox) {
    const entrepriseId = changedCheckbox.dataset.entrepriseId;
    const moduleId = changedCheckbox.dataset.moduleId;

    if (moduleId && entrepriseId) {
        const moduleCheckbox = document.getElementById(
            `module_${entrepriseId}_${moduleId}`
        );
        if (moduleCheckbox) {
            const modulePermissions = document.querySelectorAll(
                `[data-module-id="${moduleId}"][data-entreprise-id="${entrepriseId}"].permission-checkbox`
            );
            updateCheckboxState(moduleCheckbox, modulePermissions);
        }
    }

    if (entrepriseId) {
        const entrepriseCheckbox = document.getElementById(
            `entreprise_${entrepriseId}`
        );
        if (entrepriseCheckbox) {
            const entreprisePermissions = document.querySelectorAll(
                `[data-entreprise-id="${entrepriseId}"].permission-checkbox`
            );
            const entrepriseModules = document.querySelectorAll(
                `[data-entreprise-id="${entrepriseId}"].module-checkbox`
            );
            const allEntrepriseElements = [
                ...entreprisePermissions,
                ...entrepriseModules,
            ];
            updateCheckboxState(
                entrepriseCheckbox,
                allEntrepriseElements
            );
        }
    }

    updateAllPermissionsCheckbox();
}

function updateCheckboxState(parentCheckbox, childElements) {
    const activeElements = Array.from(childElements).filter(
        (cb) => !cb.disabled
    );
    const checkedElements = activeElements.filter((cb) => cb.checked);
    const totalElements = activeElements.length;

    if (totalElements === 0) {
        parentCheckbox.checked = false;
        parentCheckbox.indeterminate = false;
    } else if (checkedElements.length === 0) {
        parentCheckbox.checked = false;
        parentCheckbox.indeterminate = false;
    } else if (checkedElements.length === totalElements) {
        parentCheckbox.checked = true;
        parentCheckbox.indeterminate = false;
    } else {
        parentCheckbox.checked = false;
        parentCheckbox.indeterminate = true;
    }
}

function updateAllPermissionsCheckbox() {
    const allPermissionsCheckbox = document.getElementById("all_permissions");
    if (!allPermissionsCheckbox) return;
    const allCheckboxes = document.querySelectorAll(
        ".permission-checkbox, .entreprise-checkbox, .module-checkbox"
    );
    const activeCheckboxes = Array.from(allCheckboxes).filter(
        (cb) => !cb.disabled
    );

    updateCheckboxState(allPermissionsCheckbox, activeCheckboxes);
}

function updateAllCheckboxStates() {
    const allPermissionCheckboxes = document.querySelectorAll(
        ".permission-checkbox"
    );
    allPermissionCheckboxes.forEach((checkbox) => {
        updateParentCheckboxes(checkbox);
    });
    updatePermissionFlags();
}

document.addEventListener("DOMContentLoaded", function () {
    const permissionCheckboxes = document.querySelectorAll(
        ".permission-checkbox"
    );
    permissionCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            updateParentCheckboxes(this);
            updatePermissionFlags();
        });
    });

    const allCheckboxes = document.querySelectorAll(
        ".entreprise-checkbox, .module-checkbox"
    );
    allCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            updatePermissionFlags();
        });
    });

    updateAllCheckboxStates();
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("profilForm");
    if (form) {
        form.addEventListener("submit", function () {
            updatePermissionFlags();
        });
    }
});
