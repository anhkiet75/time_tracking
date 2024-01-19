// resources/js/components/qr-ranges.js
function qrRangesInputFormComponent({ state, splitKeys }) {
  return {
    newTag: "",
    state,
    ranges: [],
    createTag: function() {
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
    deleteTag: function(tagToDelete) {
      const originalTag = tagToDelete;
      tagToDelete = tagToDelete.trim();
      if (!tagToDelete.includes("-"))
        tagToDelete = `${tagToDelete}-${tagToDelete}`;
      if (this.ranges.hasOwnProperty(tagToDelete) && this.ranges[tagToDelete].length) {
        new FilamentNotification().title(
          `Delete QR range failed. QR code ${this.ranges[tagToDelete].join(",")} ${this.ranges[tagToDelete].length > 1 ? "are" : "is"} used.`
        ).duration(5e3).danger().send();
      } else
        this.state = this.state.filter((tag) => tag !== originalTag);
    },
    reorderTags: function(event) {
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
          const pattern = splitKeys.map(
            (key) => key.replace(/[/\-\\^$*+?.()|[\]{}]/g, "\\$&")
          ).join("|");
          this.newTag.split(new RegExp(pattern, "g")).forEach((tag) => {
            this.newTag = tag;
            this.createTag();
          });
        });
      }
    }
  };
}
export {
  qrRangesInputFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vY29tcG9uZW50cy9xci1yYW5nZXMuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIHFyUmFuZ2VzSW5wdXRGb3JtQ29tcG9uZW50KHtzdGF0ZSwgc3BsaXRLZXlzfSkge1xyXG4gICAgcmV0dXJuIHtcclxuICAgICAgICBuZXdUYWc6IFwiXCIsXHJcblxyXG4gICAgICAgIHN0YXRlLFxyXG5cclxuICAgICAgICByYW5nZXM6IFtdLFxyXG5cclxuICAgICAgICBjcmVhdGVUYWc6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdGhpcy5uZXdUYWcgPSB0aGlzLm5ld1RhZy50cmltKCk7XHJcblxyXG4gICAgICAgICAgICBpZiAodGhpcy5uZXdUYWcgPT09IFwiXCIpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgaWYgKHRoaXMuc3RhdGUuaW5jbHVkZXModGhpcy5uZXdUYWcpKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLm5ld1RhZyA9IFwiXCI7XHJcblxyXG4gICAgICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICB0aGlzLnN0YXRlLnB1c2godGhpcy5uZXdUYWcpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5uZXdUYWcgPSBcIlwiO1xyXG4gICAgICAgIH0sXHJcblxyXG4gICAgICAgIGRlbGV0ZVRhZzogZnVuY3Rpb24gKHRhZ1RvRGVsZXRlKSB7XHJcbiAgICAgICAgICAgIGNvbnN0IG9yaWdpbmFsVGFnID0gdGFnVG9EZWxldGU7XHJcbiAgICAgICAgICAgIHRhZ1RvRGVsZXRlID0gdGFnVG9EZWxldGUudHJpbSgpO1xyXG4gICAgICAgICAgICBpZiAoIXRhZ1RvRGVsZXRlLmluY2x1ZGVzKFwiLVwiKSkgdGFnVG9EZWxldGUgPSBgJHt0YWdUb0RlbGV0ZX0tJHt0YWdUb0RlbGV0ZX1gO1xyXG4gICAgICAgICAgICBpZiAoXHJcbiAgICAgICAgICAgICAgICB0aGlzLnJhbmdlcy5oYXNPd25Qcm9wZXJ0eSh0YWdUb0RlbGV0ZSkgJiZcclxuICAgICAgICAgICAgICAgIHRoaXMucmFuZ2VzW3RhZ1RvRGVsZXRlXS5sZW5ndGhcclxuICAgICAgICAgICAgKSB7XHJcbiAgICAgICAgICAgICAgICBuZXcgRmlsYW1lbnROb3RpZmljYXRpb24oKVxyXG4gICAgICAgICAgICAgICAgICAgIC50aXRsZShcclxuICAgICAgICAgICAgICAgICAgICAgICAgYERlbGV0ZSBRUiByYW5nZSBmYWlsZWQuIFFSIGNvZGUgJHt0aGlzLnJhbmdlc1tcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRhZ1RvRGVsZXRlXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBdLmpvaW4oXCIsXCIpfSAke1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5yYW5nZXNbdGFnVG9EZWxldGVdLmxlbmd0aCA+IDEgPyBcImFyZVwiIDogXCJpc1wiXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gdXNlZC5gXHJcbiAgICAgICAgICAgICAgICAgICAgKVxyXG4gICAgICAgICAgICAgICAgICAgIC5kdXJhdGlvbig1MDAwKVxyXG4gICAgICAgICAgICAgICAgICAgIC5kYW5nZXIoKVxyXG4gICAgICAgICAgICAgICAgICAgIC5zZW5kKCk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB0aGlzLnN0YXRlID0gdGhpcy5zdGF0ZS5maWx0ZXIoKHRhZykgPT4gdGFnICE9PSBvcmlnaW5hbFRhZyk7XHJcbiAgICAgICAgfSxcclxuXHJcbiAgICAgICAgcmVvcmRlclRhZ3M6IGZ1bmN0aW9uIChldmVudCkge1xyXG4gICAgICAgICAgICBjb25zdCByZW9yZGVyZWQgPSB0aGlzLnN0YXRlLnNwbGljZShldmVudC5vbGRJbmRleCwgMSlbMF07XHJcbiAgICAgICAgICAgIHRoaXMuc3RhdGUuc3BsaWNlKGV2ZW50Lm5ld0luZGV4LCAwLCByZW9yZGVyZWQpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5zdGF0ZSA9IFsuLi50aGlzLnN0YXRlXTtcclxuICAgICAgICB9LFxyXG5cclxuICAgICAgICBpbnB1dDoge1xyXG4gICAgICAgICAgICBbXCJ4LW9uOmJsdXJcIl06IFwiY3JlYXRlVGFnKClcIixcclxuICAgICAgICAgICAgW1wieC1tb2RlbFwiXTogXCJuZXdUYWdcIixcclxuICAgICAgICAgICAgW1wieC1vbjprZXlkb3duXCJdKGV2ZW50KSB7XHJcbiAgICAgICAgICAgICAgICBpZiAoW1wiRW50ZXJcIiwgLi4uc3BsaXRLZXlzXS5pbmNsdWRlcyhldmVudC5rZXkpKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuICAgICAgICAgICAgICAgICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5jcmVhdGVUYWcoKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgW1wieC1vbjpwYXN0ZVwiXSgpIHtcclxuICAgICAgICAgICAgICAgIHRoaXMuJG5leHRUaWNrKCgpID0+IHtcclxuICAgICAgICAgICAgICAgICAgICBpZiAoc3BsaXRLZXlzLmxlbmd0aCA9PT0gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmNyZWF0ZVRhZygpO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgcGF0dGVybiA9IHNwbGl0S2V5c1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAubWFwKChrZXkpID0+XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBrZXkucmVwbGFjZSgvWy9cXC1cXFxcXiQqKz8uKCl8W1xcXXt9XS9nLCBcIlxcXFwkJlwiKVxyXG4gICAgICAgICAgICAgICAgICAgICAgICApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC5qb2luKFwifFwiKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5uZXdUYWdcclxuICAgICAgICAgICAgICAgICAgICAgICAgLnNwbGl0KG5ldyBSZWdFeHAocGF0dGVybiwgXCJnXCIpKVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAuZm9yRWFjaCgodGFnKSA9PiB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLm5ld1RhZyA9IHRhZztcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmNyZWF0ZVRhZygpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICB9LFxyXG4gICAgICAgIH0sXHJcbiAgICB9O1xyXG59XHJcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBZSxTQUFSLDJCQUE0QyxFQUFDLE9BQU8sVUFBUyxHQUFHO0FBQ25FLFNBQU87QUFBQSxJQUNILFFBQVE7QUFBQSxJQUVSO0FBQUEsSUFFQSxRQUFRLENBQUM7QUFBQSxJQUVULFdBQVcsV0FBWTtBQUNuQixXQUFLLFNBQVMsS0FBSyxPQUFPLEtBQUs7QUFFL0IsVUFBSSxLQUFLLFdBQVcsSUFBSTtBQUNwQjtBQUFBLE1BQ0o7QUFFQSxVQUFJLEtBQUssTUFBTSxTQUFTLEtBQUssTUFBTSxHQUFHO0FBQ2xDLGFBQUssU0FBUztBQUVkO0FBQUEsTUFDSjtBQUVBLFdBQUssTUFBTSxLQUFLLEtBQUssTUFBTTtBQUUzQixXQUFLLFNBQVM7QUFBQSxJQUNsQjtBQUFBLElBRUEsV0FBVyxTQUFVLGFBQWE7QUFDOUIsWUFBTSxjQUFjO0FBQ3BCLG9CQUFjLFlBQVksS0FBSztBQUMvQixVQUFJLENBQUMsWUFBWSxTQUFTLEdBQUc7QUFBRyxzQkFBYyxHQUFHLFdBQVcsSUFBSSxXQUFXO0FBQzNFLFVBQ0ksS0FBSyxPQUFPLGVBQWUsV0FBVyxLQUN0QyxLQUFLLE9BQU8sV0FBVyxFQUFFLFFBQzNCO0FBQ0UsWUFBSSxxQkFBcUIsRUFDcEI7QUFBQSxVQUNHLG1DQUFtQyxLQUFLLE9BQ3BDLFdBQ0EsRUFBRSxLQUFLLEdBQUcsQ0FBQyxJQUNYLEtBQUssT0FBTyxXQUFXLEVBQUUsU0FBUyxJQUFJLFFBQVEsSUFDbEQ7QUFBQSxRQUNKLEVBQ0MsU0FBUyxHQUFJLEVBQ2IsT0FBTyxFQUNQLEtBQUs7QUFBQSxNQUNkO0FBQU8sYUFBSyxRQUFRLEtBQUssTUFBTSxPQUFPLENBQUMsUUFBUSxRQUFRLFdBQVc7QUFBQSxJQUN0RTtBQUFBLElBRUEsYUFBYSxTQUFVLE9BQU87QUFDMUIsWUFBTSxZQUFZLEtBQUssTUFBTSxPQUFPLE1BQU0sVUFBVSxDQUFDLEVBQUUsQ0FBQztBQUN4RCxXQUFLLE1BQU0sT0FBTyxNQUFNLFVBQVUsR0FBRyxTQUFTO0FBRTlDLFdBQUssUUFBUSxDQUFDLEdBQUcsS0FBSyxLQUFLO0FBQUEsSUFDL0I7QUFBQSxJQUVBLE9BQU87QUFBQSxNQUNILENBQUMsV0FBVyxHQUFHO0FBQUEsTUFDZixDQUFDLFNBQVMsR0FBRztBQUFBLE1BQ2IsQ0FBQyxjQUFjLEVBQUUsT0FBTztBQUNwQixZQUFJLENBQUMsU0FBUyxHQUFHLFNBQVMsRUFBRSxTQUFTLE1BQU0sR0FBRyxHQUFHO0FBQzdDLGdCQUFNLGVBQWU7QUFDckIsZ0JBQU0sZ0JBQWdCO0FBRXRCLGVBQUssVUFBVTtBQUFBLFFBQ25CO0FBQUEsTUFDSjtBQUFBLE1BQ0EsQ0FBQyxZQUFZLElBQUk7QUFDYixhQUFLLFVBQVUsTUFBTTtBQUNqQixjQUFJLFVBQVUsV0FBVyxHQUFHO0FBQ3hCLGlCQUFLLFVBQVU7QUFFZjtBQUFBLFVBQ0o7QUFFQSxnQkFBTSxVQUFVLFVBQ1g7QUFBQSxZQUFJLENBQUMsUUFDRixJQUFJLFFBQVEsMEJBQTBCLE1BQU07QUFBQSxVQUNoRCxFQUNDLEtBQUssR0FBRztBQUViLGVBQUssT0FDQSxNQUFNLElBQUksT0FBTyxTQUFTLEdBQUcsQ0FBQyxFQUM5QixRQUFRLENBQUMsUUFBUTtBQUNkLGlCQUFLLFNBQVM7QUFFZCxpQkFBSyxVQUFVO0FBQUEsVUFDbkIsQ0FBQztBQUFBLFFBQ1QsQ0FBQztBQUFBLE1BQ0w7QUFBQSxJQUNKO0FBQUEsRUFDSjtBQUNKOyIsCiAgIm5hbWVzIjogW10KfQo=