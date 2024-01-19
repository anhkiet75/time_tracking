export default function qrRangesInputFormComponent({state, splitKeys}) {
    return {
        newTag: "",

        state,

        ranges: [],

        createTag: function () {
            this.newTag = this.newTag.trim();

            if (this.newTag === "") {
                return;
            }

            if (this.state.includes(this.newTag)) {
                this.newTag = "";

                return;
            }

            this.state.push(this.newTag);

            this.newTag = "";
        },

        deleteTag: function (tagToDelete) {
            const originalTag = tagToDelete;
            tagToDelete = tagToDelete.trim();
            if (!tagToDelete.includes("-")) tagToDelete = `${tagToDelete}-${tagToDelete}`;
            if (
                this.ranges.hasOwnProperty(tagToDelete) &&
                this.ranges[tagToDelete].length
            ) {
                new FilamentNotification()
                    .title(
                        `Delete QR range failed. QR code ${this.ranges[
                            tagToDelete
                            ].join(",")} ${
                            this.ranges[tagToDelete].length > 1 ? "are" : "is"
                        } used.`
                    )
                    .duration(5000)
                    .danger()
                    .send();
            } else this.state = this.state.filter((tag) => tag !== originalTag);
        },

        reorderTags: function (event) {
            const reordered = this.state.splice(event.oldIndex, 1)[0];
            this.state.splice(event.newIndex, 0, reordered);

            this.state = [...this.state];
        },

        input: {
            ["x-on:blur"]: "createTag()",
            ["x-model"]: "newTag",
            ["x-on:keydown"](event) {
                if (["Enter", ...splitKeys].includes(event.key)) {
                    event.preventDefault();
                    event.stopPropagation();

                    this.createTag();
                }
            },
            ["x-on:paste"]() {
                this.$nextTick(() => {
                    if (splitKeys.length === 0) {
                        this.createTag();

                        return;
                    }

                    const pattern = splitKeys
                        .map((key) =>
                            key.replace(/[/\-\\^$*+?.()|[\]{}]/g, "\\$&")
                        )
                        .join("|");

                    this.newTag
                        .split(new RegExp(pattern, "g"))
                        .forEach((tag) => {
                            this.newTag = tag;

                            this.createTag();
                        });
                });
            },
        },
    };
}
