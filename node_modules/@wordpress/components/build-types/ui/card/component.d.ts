export default ConnectedCard;
/**
 * `Card` is a layout component, providing a flexible and extensible content container.
 *
 * `Card` provides convenient sub-components such as `CardBody`, `CardHeader`, and `CardFooter`.
 *
 * @example
 * ```jsx
 * import {
 *   Card,
 *   CardHeader,
 *   CardBody,
 *   CardFooter,
 *   Text,
 *   Heading,
 * } from `@wordpress/components`;
 *
 * function Example() {
 *   return (
 *     <Card>
 *       <CardHeader>
 *         <Heading size={ 4 }>Card Title</Heading>
 *       </CardHeader>
 *       <CardBody>
 *         <Text>Card Content</Text>
 *       </CardBody>
 *       <CardFooter>
 *         <Text>Card Footer</Text>
 *       </CardFooter>
 *     </Card>
 *   );
 * }
 * ```
 */
declare const ConnectedCard: import("../context").PolymorphicComponent<"div", import("./types").CardProps>;
//# sourceMappingURL=component.d.ts.map