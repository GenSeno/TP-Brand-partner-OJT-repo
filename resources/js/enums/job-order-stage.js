export const JobOrderStage = {
    NewOrder: 'new',
    Artist: 'artist',
    Printing: 'printing',
    HeatPress: 'heatpress',
    Sewing: 'sewing',
    Packing: 'packing',
    Dispatching: 'dispatching',
    Completed: 'completed',
    Cancelled: 'cancelled',
};

export const JobOrderStageOptions = {
    new: {
        label: 'New',
        color: 'info',
        icon: 'feather-plus',
    },
    artist: {
        label: 'Artist',
        color: 'primary',
        icon: 'feather-edit-3',
    },
    printing: {
        label: 'Printing',
        color: 'warning',
        icon: 'feather-printer',
    },
    heatpress: {
        label: 'Heat Press/Cutting',
        color: 'danger',
        icon: 'feather-zap',
    },
    sewing: {
        label: 'Sewing',
        color: 'secondary',
        icon: 'feather-scissors',
    },
    packing: {
        label: 'Packing',
        color: 'success',
        icon: 'feather-package',
    },
    dispatching: {
        label: 'Dispatching',
        color: 'dark',
        icon: 'feather-truck',
    },
    completed: {
        label: 'Completed',
        color: 'success',
        icon: 'feather-check-circle',
    },
    cancelled: {
        label: 'Cancelled',
        color: 'danger',
        icon: 'feather-x-circle',
    },
};
