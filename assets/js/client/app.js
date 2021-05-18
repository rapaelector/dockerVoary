import { SymfonyCollections } from '../shared/symfony_collection';

$(function () {
    var collectionOptions = {
		protoWrapper: 'div',
		label: '',
		remove: {
			content: 'supprimer',
			attr: {
				class: 'btn btn-sm btn-app-secondary',
			},
		},
		add: {
			content: '<span class="material-icons">add</span> ajouter contact',
			attr: {
				class: 'btn btn-sm btn-app-primary',
                id: 'add-contact'
			},
			container: '#add-button-container',
		},
        prototypeAttr: {
            class: 'col-sm-6 mt-3'
        }
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