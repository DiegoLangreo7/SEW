class Memoria {
    constructor() {
        this.elements = {
            RedBull: "https://upload.wikimedia.org/wikipedia/de/c/c4/Red_Bull_Racing_logo.svg",
            McLaren: "https://upload.wikimedia.org/wikipedia/en/6/66/McLaren_Racing_logo.svg",
            Alpine: "https://upload.wikimedia.org/wikipedia/fr/b/b7/Alpine_F1_Team_2021_Logo.svg",
            AstonMartin: "https://upload.wikimedia.org/wikipedia/fr/7/72/Aston_Martin_Aramco_Cognizant_F1.svg",
            Ferrari: "https://upload.wikimedia.org/wikipedia/de/c/c0/Scuderia_Ferrari_Logo.svg",
            Mercedes: "https://upload.wikimedia.org/wikipedia/commons/f/fb/Mercedes_AMG_Petronas_F1_Logo.svg"
        };

        this.cards = [];
        this.hasFlippedCard = false;
        this.lockBoard = false;
        this.firstCard = null;
        this.secondCard = null;

        this.createElements();
        this.addEventListeners();
        this.deleteHelp();
        this.createBotton();
    }

    createElements() {
        const memoryCardsContainer = document.querySelector('section');

        while (memoryCardsContainer.firstChild) {
            memoryCardsContainer.removeChild(memoryCardsContainer.firstChild);
        }

        memoryCardsContainer.innerHTML  =`<h3>Juego de Memoria</h3>`;

        const elementsArray = Object.entries(this.elements).flatMap(([element, src]) => [
            { element, src },
            { element, src }
        ]);

        this.shuffleElements(elementsArray);

        elementsArray.forEach(({ element, src }) => {
            const card = document.createElement('article');
            card.setAttribute('data-element', element);
            card.innerHTML = `
                <h4>Memory Card</h4>
                <img src="${src}" alt="${element}">
            `;
            memoryCardsContainer.appendChild(card);
            this.cards.push(card);
        });
    }

    shuffleElements(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    addEventListeners() {
        this.cards.forEach(card => {
            card.addEventListener("click", this.flipCard.bind(this, card));
        });
    }

    flipCard(card) {
        if (this.lockBoard || card === this.firstCard || card.getAttribute("data-state") === "revealed") return;

        card.setAttribute('data-state', 'flip');

        if (!this.hasFlippedCard) {
            this.hasFlippedCard = true;
            this.firstCard = card;
            return;
        }
        this.lockBoard = true;
        this.secondCard = card;
        this.checkForMatch();
    }

    checkForMatch() {
        const isMatch = this.firstCard.getAttribute("data-element") === this.secondCard.getAttribute("data-element");

        isMatch ? this.disableCards() : this.unflipCards();
    }

    disableCards() {
	    this.firstCard.setAttribute("data-state", "revealed");
        this.secondCard.setAttribute("data-state", "revealed");

        this.firstCard.removeEventListener("click", this.flipCard.bind(this, this.firstCard));
        this.secondCard.removeEventListener("click", this.flipCard.bind(this, this.secondCard));


        setTimeout(() => {
            this.resetBoard();
        }, 100); 
    }


    unflipCards() {
        setTimeout(() => {
            this.firstCard.removeAttribute("data-state");
            this.secondCard.removeAttribute("data-state");
            this.resetBoard();
        }, 600);
    }

    resetBoard() {
        [this.hasFlippedCard, this.lockBoard, this.firstCard, this.secondCard] = [false, false, null, null];
    }

    showHelp() {
        this.deleteHelp();
        const ayudaContainer = document.createElement("article");
    
        ayudaContainer.innerHTML = `
            <p>
                Este es un juego de memoria. Las reglas son simples:
            </p>
            <ul>
                <li>Haz clic en una carta para voltearla.</li>
                <li>Encuentra su pareja volteando otra carta.</li>
                <li>Si las cartas coinciden, permanecerán visibles.</li>
                <li>Si no coinciden, ambas cartas se voltearán nuevamente.</li>
            </ul>
            <p>¡Tu objetivo es encontrar todas las parejas con el menor número de intentos posible!</p>
        `;
    
        const main = document.querySelector('main');
        main.parentNode.insertBefore(ayudaContainer, main);
    
        this.deleteBotton();
    }

    createBotton(){
        const botones = document.querySelectorAll('button');
        if (botones.length > 1) {
            return;
        }

        const helpButton = document.createElement("button");
        helpButton.innerHTML = "¿Como Jugar?";
        helpButton.onclick = () => this.showHelp();
    
        const main = document.querySelector('main');
        main.parentNode.insertBefore(helpButton, main);
    }

    deleteBotton() {
        const botones = document.querySelectorAll('button');
        const botonAyuda = botones[1];
        if (botonAyuda) {
            botonAyuda.remove();
        }
    }

    deleteHelp() {
        const yaExistente = document.querySelector('body > article');
        if (yaExistente) {
            yaExistente.remove();
        }
    }
}

let memoriaJuego = new Memoria();
