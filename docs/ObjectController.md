# Object Controller

The `ObjectController` is the primary way to interact with your created objects.

## Options
| key                | type   | default    | description                                                                       |
|--------------------|--------|------------|-----------------------------------------------------------------------------------|
| includeUnpublished | bool   | `false`    | If true, all methods include objects marked as unpublished                        |
| sortOrder          | string | `"desc"`   | Sets whether to sort the returned objects by highest `sortOrder` first, or lowest |