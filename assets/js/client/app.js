import { SymfonyCollections } from '../shared/symfony_collection';

$(function () {
    var collectionOptions = {
		protoWrapper: '.contact-entry-container',
		label: '',
		remove: {
			content: `<span
				class="material-icon"
				data-toggle="tooltip"
				data-placement="bottom" 
				title="Supprimer la contact" data-container="body">
					Supprimer
				</span>`,
			attr: {
				class: 'btn btn-sm btn-link',
				type: 'button'
			},
			container: '.delete-container',
		},
		add: {
			content: '<span class="material-icons">add</span> <span>Ajouter contact</span>',
			attr: {
				class: 'btn btn-sm btn-app-secondary d-flex align-items-center',
                id: 'add-contact'
			},
			container: '#add-button-container',
		},
        prototypeAttr: {
            class: 'container-fluid'
        },
		wrapperAdditionalClass: 'container-fluid',
    };
    try {
		var clientContactsContainer = '#client_contacts';
        var clientContactsForm = new SymfonyCollections($.extend(true, {}, collectionOptions, {
            selector: clientContactsContainer,
        }));
		$(clientContactsContainer).addClass('row');

    } catch (e) {
        console.warn('Exception catch : ' , e);
    }
})