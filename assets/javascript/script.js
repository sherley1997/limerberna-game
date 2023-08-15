// alert('test');

document.addEventListener("DOMContentLoaded", () => {
  const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector(
      "." + e.currentTarget.dataset.collectionHolderClass
    );
    const item = document.createElement("li");
    item.innerHTML = collectionHolder.dataset.prototype.replace(
      /__name__/g,
      collectionHolder.dataset.index
    );
    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;
  };

  const addQtyFormDeleteLink = (item) => {
    const removeFormButton = document.createElement("button");
    removeFormButton.innerText = "Supprimer";
    removeFormButton.classList = "delete_item_link";
    item.append(removeFormButton);
    removeFormButton.addEventListener("click", (e) => {
      e.preventDefault();
      // remove the li for the Qty form
      item.remove();
    });
  };

  document.querySelectorAll(".add_item_link").forEach((btn) => {
    // btn.addEventListener("click", console.log('hello'));
    btn.addEventListener("click", addFormToCollection);
  });

  document.querySelectorAll("ul.plateformes li").forEach((plateforme) => {
    addQtyFormDeleteLink(plateforme);
  });

});
